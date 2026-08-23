<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SalesSearchRequest;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesController extends Controller
{
    public function index(SalesSearchRequest $request)
    {
        $filters = $request->validated();
        [$from, $to] = $this->resolvePeriod($filters);

        $paidQuery = $this->baseQuery($filters, $from, $to);

        $summary = $this->buildSummary($filters, $from, $to);

        $orders = (clone $paidQuery)
            ->with('items')
            ->orderBy('paid_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.sales.index', compact('orders', 'summary', 'filters', 'from', 'to'));
    }

    public function export(SalesSearchRequest $request): StreamedResponse
    {
        $filters = $request->validated();
        [$from, $to] = $this->resolvePeriod($filters);

        $query = $this->baseQuery($filters, $from, $to)->with('items');

        $fileName = sprintf('sales_%s_%s.csv', $from->format('Ymd'), $to->format('Ymd'));

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename={$fileName}",
        ];

        $callback = function () use ($query) {
            $handle = fopen('php://output', 'w');

            // Excelで開いた際の文字化け対策としてBOMを付与
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                '決済ID', '注文ID', '決済日時', '購入者名', 'メールアドレス',
                '商品名', '商品数量', '商品単価', '商品小計',
                '商品合計', '送料', '割引額', '税額', '支払総額',
                '決済方法', '決済ステータス', '注文ステータス',
            ]);

            // 大量データでも一度にメモリへ読み込まないよう、200件ずつ区切って書き出す
            $query->orderBy('id')->chunk(200, function ($orders) use ($handle) {
                foreach ($orders as $order) {
                    $orderColumns = [
                        $order->id,
                        $order->order_number,
                        optional($order->paid_at)->format('Y-m-d H:i:s'),
                        $order->recipient_name,
                        $order->email,
                    ];

                    // 送料・割引額・税額は現行DBに内訳を保持していないため 0 固定（total_price は税込・送料込みの合計）
                    $amountColumns = [
                        $order->total_price,
                        0,
                        0,
                        0,
                        $order->total_price,
                        $order->payment_method_label,
                        $order->payment_status_label,
                        $order->status_label,
                    ];

                    if ($order->items->isEmpty()) {
                        fputcsv($handle, array_merge($orderColumns, ['', '', '', ''], $amountColumns));
                        continue;
                    }

                    foreach ($order->items as $item) {
                        fputcsv($handle, array_merge($orderColumns, [
                            $item->product_name,
                            $item->quantity,
                            $item->price,
                            $item->subtotal,
                        ], $amountColumns));
                    }
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─────────────────────────────────────────────────────
    // 内部メソッド
    // ─────────────────────────────────────────────────────

    /**
     * 売上管理の対象となる注文（決済完了済み・指定期間・任意の注文ステータス）を返す
     */
    private function baseQuery(array $filters, Carbon $from, Carbon $to)
    {
        return Order::query()
            ->paid()
            ->whereBetween('paid_at', [$from, $to])
            ->when($filters['status'] ?? null, function ($query, $value) {
                $query->where('status', $value);
            });
    }

    /** 検索期間を確定する。未指定の場合は「当月1日〜今日」をデフォルトとする */
    private function resolvePeriod(array $filters): array
    {
        $from = isset($filters['date_from'])
            ? Carbon::parse($filters['date_from'])->startOfDay()
            : now()->startOfMonth()->startOfDay();

        $to = isset($filters['date_to'])
            ? Carbon::parse($filters['date_to'])->endOfDay()
            : now()->endOfDay();

        return [$from, $to];
    }

    /**
     * 期間内の売上サマリーを DB 側の集計（SUM/COUNT）で算出する
     * 全件を PHP 側に取得して合計する実装は避ける
     */
    private function buildSummary(array $filters, Carbon $from, Carbon $to): array
    {
        $paidQuery = $this->baseQuery($filters, $from, $to);

        $grossSales   = (clone $paidQuery)->sum('total_price');
        $paymentCount = (clone $paidQuery)->count();

        // 支払済みかつキャンセルされた注文＝返金相当として扱う（現行DBに専用の返金額カラムがないための近似）
        $refundedQuery  = (clone $paidQuery)->where('status', 'cancelled');
        $refundedAmount = (clone $refundedQuery)->sum('total_price');
        $refundedCount  = (clone $refundedQuery)->count();

        // 注文件数：決済有無に関わらず、期間内に作成された注文の総数（決済件数との比較用）
        $orderCount = Order::query()->whereBetween('created_at', [$from, $to])->count();

        return [
            'gross_sales'     => $grossSales,
            'net_sales'       => $grossSales - $refundedAmount,
            'refunded_amount' => $refundedAmount,
            'refunded_count'  => $refundedCount,
            'payment_count'   => $paymentCount,
            'order_count'     => $orderCount,
            'tax_amount'      => 0,
            'shipping_amount' => 0,
            'discount_amount' => 0,
        ];
    }
}
