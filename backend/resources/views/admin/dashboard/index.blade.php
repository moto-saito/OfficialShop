@extends('admin.layouts.app')

@section('title', 'ダッシュボード')

@section('content')

{{-- サマリーカード --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl shadow-sm px-6 py-5">
        <p class="text-xs text-gray-500 mb-1">総注文数</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalOrderCount) }}<span class="text-sm font-normal text-gray-400 ml-1">件</span></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm px-6 py-5">
        <p class="text-xs text-gray-500 mb-1">本日の注文数</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($todayOrderCount) }}<span class="text-sm font-normal text-gray-400 ml-1">件</span></p>
    </div>
    <div class="bg-white rounded-xl shadow-sm px-6 py-5">
        <p class="text-xs text-gray-500 mb-1">売上合計</p>
        <p class="text-2xl font-bold text-gray-900">¥{{ number_format($totalSales) }}</p>
    </div>
    <div class="bg-white rounded-xl shadow-sm px-6 py-5">
        <p class="text-xs text-gray-500 mb-1">未発送件数</p>
        <p class="text-2xl font-bold text-gray-900">{{ number_format($unshippedCount) }}<span class="text-sm font-normal text-gray-400 ml-1">件</span></p>
    </div>
</div>

{{-- 最新注文5件 --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700">最新注文</h2>
        <a href="{{ route('admin.orders.index') }}" class="text-xs text-indigo-600 hover:text-indigo-700 transition">すべて見る →</a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-6 py-3 font-medium text-gray-500">注文番号</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-40">注文日</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500">購入者氏名</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-28">注文金額</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-28">ステータス</th>
                <th class="px-6 py-3 w-24"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($latestOrders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-medium text-gray-800">{{ $order->order_number }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('Y/m/d H:i') }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $order->recipient_name }}</td>
                    <td class="px-6 py-4 text-gray-700">{{ $order->formatted_total_price }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.orders.show', $order) }}" class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                            詳細
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-gray-400">注文がありません</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
