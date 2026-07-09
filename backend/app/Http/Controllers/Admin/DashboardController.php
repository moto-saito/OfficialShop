<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

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

        return view('admin.dashboard.index', compact(
            'totalOrderCount',
            'todayOrderCount',
            'totalSales',
            'unshippedCount',
            'latestOrders'
        ));
    }
}
