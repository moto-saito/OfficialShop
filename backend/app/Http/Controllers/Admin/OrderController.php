<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderSearchRequest;
use App\Models\Order;
use App\Services\YamatoB2Csv;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(OrderSearchRequest $request)
    {
        $filters = $request->validated();

        // 一覧はデフォルトでは表示せず、検索したときだけ表示する。
        // 「検索」ボタン（searched=1）か、URL に検索条件が付いている場合を「検索した」とみなす。
        // 条件を空のまま検索した場合は全件を表示する。
        $searched = $request->boolean('searched')
            || collect($filters)->contains(fn ($value) => $value !== null && $value !== '');

        $orders = $searched
            ? Order::search($filters)
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString()
            : null;

        return view('admin.orders.index', compact('orders', 'filters', 'searched'));
    }

    public function show(Order $order)
    {
        // 商品画像・商品名の表示に product を使うため items.product を eager load
        $order->load('items.product');

        return view('admin.orders.show', compact('order'));
    }

    /**
     * ヤマト運輸 B2クラウドに取り込む送り状 CSV（基本レイアウト・Shift_JIS）を出力する。
     * 送り状に収まらない項目や .env の設定漏れがあれば、注文詳細に戻って理由を表示する。
     */
    public function b2Csv(Order $order, YamatoB2Csv $b2)
    {
        $order->load('items');

        $row = $b2->row($order);
        if ($row === null) {
            return redirect()->route('admin.orders.show', $order)
                ->with('error', array_merge(['B2クラウド用CSVを出力できません。'], $b2->errors()));
        }

        $handle = fopen('php://temp', 'r+');
        $lines = config('yamato.b2.header_row') ? [YamatoB2Csv::COLUMNS, $row] : [$row];
        foreach ($lines as $line) {
            fputcsv($handle, array_map([YamatoB2Csv::class, 'toSjis'], $line), ',', '"', '', "\r\n");
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $fileName = 'b2_' . str_replace('-', '', $order->order_number) . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=Shift_JIS',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ]);
    }

    /** PDF 用の日本語フォント（Docker イメージに fonts-ipafont-gothic で導入） */
    private const PDF_FONT_PATH = '/usr/share/fonts/opentype/ipafont-gothic/ipag.ttf';

    /** 1ページに収まらない場合に縮小する最小倍率（無限ループ防止の下限） */
    private const PDF_MIN_SCALE = 0.1;

    /** 収まる最大の倍率を探す二分探索の回数 */
    private const PDF_SEARCH_STEPS = 5;

    /** PDF に埋め込む商品画像の長辺（px） */
    private const PDF_THUMB_SIZE = 200;

    /**
     * 注文詳細（注文情報・購入者情報・商品一覧）を A4 1枚の PDF で出力する。
     * 商品数が多く1ページに収まらない場合は、収まるまで全体を縮小して描画し直す。
     */
    public function pdf(Order $order): Response
    {
        $order->load('items.product');

        // 画像は data URI で埋め込む（ストレージが local / s3 どちらでも動き、dompdf にリモート取得を許可しなくて済む）
        $disk = Storage::disk('public');
        $images = [];
        foreach ($order->items as $item) {
            $path = $item->product?->image_path;
            try {
                if ($path && $disk->exists($path)) {
                    $images[$item->id] = $this->pdfThumbnail($disk->get($path));
                }
            } catch (\Throwable $e) {
                report($e); // 画像が読めなくても PDF は出力する
            }
        }

        $render = function (float $scale) use ($order, $images): array {
            $pdf = Pdf::setOption([
                    'chroot'                  => [base_path(), dirname(self::PDF_FONT_PATH)],
                    'isRemoteEnabled'         => false,
                    'isFontSubsettingEnabled' => true,
                ])
                ->loadView('admin.orders.pdf', [
                    'order'    => $order,
                    'images'   => $images,
                    'scale'    => $scale,
                    'fontPath' => self::PDF_FONT_PATH,
                ])
                ->setPaper('a4', 'portrait');
            $pdf->render();

            return [$pdf, $pdf->getDomPDF()->getCanvas()->get_page_count()];
        };

        // 1) 1ページに収まる倍率まで縮める。高さはほぼ倍率に比例するので、ページ数から次の倍率を見積もる
        $scale = 1.0;
        $tooBig = null; // 収まらなかった倍率
        [$pdf, $pages] = $render($scale);
        while ($pages > 1 && $scale > self::PDF_MIN_SCALE) {
            $tooBig = $scale;
            $scale = max(self::PDF_MIN_SCALE, round($scale * min(0.85, 0.95 / $pages), 3));
            [$pdf, $pages] = $render($scale);
        }

        // 2) 見積もりは余白（固定）の分だけ縮めすぎるので、収まる最大の倍率を二分探索で探す
        if ($pages === 1 && $tooBig !== null) {
            for ($i = 0; $i < self::PDF_SEARCH_STEPS; $i++) {
                $mid = round(($scale + $tooBig) / 2, 3);
                [$candidate, $candidatePages] = $render($mid);
                if ($candidatePages === 1) {
                    [$scale, $pdf] = [$mid, $candidate];
                } else {
                    $tooBig = $mid;
                }
            }
        }

        return $pdf->download('order_' . $order->order_number . '.pdf');
    }

    /**
     * 商品画像を PDF 用のサムネイル（data URI）に縮小する。
     * 元画像をそのまま埋め込むと PDF が数 MB になるため、長辺 PDF_THUMB_SIZE px の JPEG にする。
     */
    private function pdfThumbnail(string $binary): ?string
    {
        $src = @imagecreatefromstring($binary);
        if ($src === false) {
            // GD で読めない形式は、dompdf が扱える形式ならそのまま埋め込む
            $mime = (new \finfo(FILEINFO_MIME_TYPE))->buffer($binary);
            return in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)
                ? 'data:' . $mime . ';base64,' . base64_encode($binary)
                : null;
        }

        $w = imagesx($src);
        $h = imagesy($src);
        $ratio = min(1, self::PDF_THUMB_SIZE / max($w, $h));
        $tw = max(1, (int) round($w * $ratio));
        $th = max(1, (int) round($h * $ratio));

        $dst = imagecreatetruecolor($tw, $th);
        // 透過 PNG は白背景に合成する（JPEG は透過を持てないため）
        imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $tw, $th, $w, $h);

        ob_start();
        imagejpeg($dst, null, 85);
        $jpeg = ob_get_clean();
        imagedestroy($src);
        imagedestroy($dst);

        return 'data:image/jpeg;base64,' . base64_encode($jpeg);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status'         => 'required|in:pending,processing,shipped,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid',
        ]);

        // 売上管理の期間集計に使う決済日時。unpaid→paid に変わった時点を記録し、paid→unpaid に戻された場合はクリアする
        if ($validated['payment_status'] === 'paid' && $order->payment_status !== 'paid') {
            $validated['paid_at'] = now();
        } elseif ($validated['payment_status'] === 'unpaid') {
            $validated['paid_at'] = null;
        }

        $order->update($validated);

        return redirect()->route('admin.orders.index')->with('success', '注文ステータスを更新しました。');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', '注文を削除しました。');
    }

    public function export(OrderSearchRequest $request): StreamedResponse
    {
        $filters = $request->validated();

        $orders = Order::search($filters)->orderBy('created_at', 'desc')->get();

        $fileName = 'orders_' . now()->format('YmdHis') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($orders) {
            $handle = fopen('php://output', 'w');

            // Excelで開いた際の文字化け対策としてBOMを付与
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, ['注文番号', '注文日', '購入者名', 'メールアドレス', '合計金額', '注文ステータス', '決済ステータス']);

            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->recipient_name,
                    $order->email,
                    $order->total_price,
                    $order->status_label,
                    $order->payment_status_label,
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
