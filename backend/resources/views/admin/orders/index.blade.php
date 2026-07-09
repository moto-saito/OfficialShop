@extends('admin.layouts.app')

@section('title', '注文管理')

@section('header-action')
    <a href="{{ route('admin.orders.export', array_filter($filters)) }}"
       class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition">
        CSV出力
    </a>
@endsection

@section('content')

{{-- 検索フォーム --}}
<div class="bg-white rounded-xl shadow-sm px-6 py-5 mb-6">
    <form method="GET" action="{{ route('admin.orders.index') }}">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-xs text-gray-500 mb-1">注文番号</label>
                <input type="text" name="order_number" value="{{ $filters['order_number'] ?? '' }}"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">購入者氏名</label>
                <input type="text" name="recipient_name" value="{{ $filters['recipient_name'] ?? '' }}"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">メールアドレス</label>
                <input type="text" name="email" value="{{ $filters['email'] ?? '' }}"
                       class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">注文ステータス</label>
                <select name="status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">すべて</option>
                    @foreach (['pending' => '受付中', 'processing' => '準備中', 'shipped' => '発送済', 'completed' => '完了', 'cancelled' => 'キャンセル'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">決済ステータス</label>
                <select name="payment_status" class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">すべて</option>
                    @foreach (['unpaid' => '未払い', 'paid' => '支払済'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['payment_status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">注文日（期間指定）</label>
                <div class="flex items-center gap-2">
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <span class="text-gray-400">〜</span>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
                検索
            </button>
            <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
                条件をクリア
            </a>
        </div>
    </form>
</div>

{{-- 注文一覧 --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">注文番号</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500 w-40">注文日</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">購入者氏名</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">メールアドレス</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500 w-28">注文金額</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500 w-28">注文ステータス</th>
                    <th class="text-left px-6 py-3 font-medium text-gray-500 w-28">決済ステータス</th>
                    <th class="px-6 py-3 w-24"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 font-medium text-gray-800">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $order->created_at->format('Y/m/d H:i') }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $order->recipient_name }}</td>
                        <td class="px-6 py-4 text-gray-500 break-all">{{ $order->email }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $order->formatted_total_price }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->status_color }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if ($order->payment_status === 'paid')
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>支払済
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>未払い
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-400">
                            該当する注文がありません
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
