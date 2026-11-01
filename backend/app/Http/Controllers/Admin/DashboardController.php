<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalOrderCount = Order::count();
        $todayOrderCount = Order::whereDate('created_at', today())->count();

        // 売上合計はキャンセルされた注文を除いて集計
        $totalSales = Order::where('status', '!=', 'cancelled')->sum('total_price');

        // 未発送件数 = 受付中・準備中（発送完了前）の注文数
        $unshippedCount = Order::whereIn('status', ['pending', 'processing'])->count();

        $latestOrders = Order::orderBy('created_at', 'desc')->limit(5)->get();

        $salesChart = $this->buildSalesChart();
        $statusChart = $this->buildStatusChart();

        return view('admin.dashboard.index', compact(
            'totalOrderCount',
            'todayOrderCount',
            'totalSales',
            'unshippedCount',
            'latestOrders',
            'salesChart',
            'statusChart'
        ));
    }

    /**
     * 売上推移グラフ用データ
     * 対象 = 注文ステータスが「発送済 or 完了」かつ 決済ステータスが「支払済」の注文
     * 期間ごとに集計粒度を変える（1ヶ月:日別 / 3ヶ月:週別 / 9ヶ月:月別）
     * MySQL / SQLite 両対応にするため、日付の丸めはPHP側で行う
     */
    private function buildSalesChart(): array
    {
        $now = Carbon::now();
        $oldest = $now->copy()->subMonthsNoOverflow(9)->startOfMonth();

        $orders = Order::paid()
            ->whereIn('status', ['shipped', 'completed'])
            ->where(function ($q) use ($oldest) {
                $q->where('paid_at', '>=', $oldest)
                  ->orWhere(function ($q) use ($oldest) {
                      $q->whereNull('paid_at')->where('created_at', '>=', $oldest);
                  });
            })
            ->get(['total_price', 'paid_at', 'created_at']);

        $points = $orders->map(fn ($o) => [
            'date'   => $o->paid_at ?? $o->created_at,
            'amount' => (int) $o->total_price,
        ]);

        return [
            '1m' => $this->aggregate($points, $now->copy()->subMonthNoOverflow()->addDay()->startOfDay(), $now, 'day'),
            '3m' => $this->aggregate($points, $now->copy()->subMonthsNoOverflow(3)->startOfWeek(), $now, 'week'),
            '9m' => $this->aggregate($points, $now->copy()->subMonthsNoOverflow(8)->startOfMonth(), $now, 'month'),
        ];
    }

    /** 期間内を指定粒度で区切り、各区間の売上合計を返す（売上0の区間も0で埋める） */
    private function aggregate($points, Carbon $from, Carbon $to, string $unit): array
    {
        $buckets = [];
        $cursor = $from->copy();
        while ($cursor <= $to) {
            $key = $this->bucketKey($cursor, $unit);
            $buckets[$key] = [
                'label'  => match ($unit) {
                    'day'   => $cursor->format('n/j'),
                    'week'  => $cursor->format('n/j') . '〜',
                    'month' => $cursor->format('Y/n'),
                },
                'amount' => 0,
            ];
            $cursor = match ($unit) {
                'day'   => $cursor->addDay(),
                'week'  => $cursor->addWeek(),
                'month' => $cursor->addMonthNoOverflow(),
            };
        }

        foreach ($points as $p) {
            if ($p['date'] < $from || $p['date'] > $to) {
                continue;
            }
            $key = $this->bucketKey($p['date'], $unit);
            if (isset($buckets[$key])) {
                $buckets[$key]['amount'] += $p['amount'];
            }
        }

        return [
            'labels' => array_column($buckets, 'label'),
            'values' => array_column($buckets, 'amount'),
        ];
    }

    private function bucketKey(Carbon $date, string $unit): string
    {
        return match ($unit) {
            'day'   => $date->format('Y-m-d'),
            'week'  => $date->copy()->startOfWeek()->format('Y-m-d'),
            'month' => $date->format('Y-m'),
        };
    }

    /**
     * 現在の注文状況（ドーナツグラフ）用データ
     * 「完了」と「発送済＋支払済」は対応不要のため対象外
     */
    private function buildStatusChart(): array
    {
        $pending = Order::where('status', 'pending')->count();
        $processing = Order::where('status', 'processing')->count();
        $shippedUnpaid = Order::where('status', 'shipped')->where('payment_status', '!=', 'paid')->count();
        $others = Order::whereNotIn('status', ['pending', 'processing', 'shipped', 'completed'])->count();

        $items = [
            ['label' => '受付中', 'count' => $pending],
            ['label' => '準備中', 'count' => $processing],
            ['label' => '発送済（未払い）', 'count' => $shippedUnpaid],
            ['label' => 'その他', 'count' => $others],
        ];

        return [
            'items' => $items,
            'total' => array_sum(array_column($items, 'count')),
        ];
    }
}
