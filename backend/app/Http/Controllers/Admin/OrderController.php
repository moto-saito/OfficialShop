<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OrderSearchRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderController extends Controller
{
    public function index(OrderSearchRequest $request)
    {
        $filters = $request->validated();

        $orders = Order::search($filters)
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'filters'));
    }

    public function show(Order $order)
    {
        // 商品画像・商品名の表示に product を使うため items.product を eager load
        $order->load('items.product');

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status'         => 'required|in:pending,processing,shipped,completed,cancelled',
            'payment_status' => 'required|in:unpaid,paid',
        ]);

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
