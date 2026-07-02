@extends('layouts.app')

@section('title', '注文詳細')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">注文詳細</h1>
        <a href="{{ route('mypage.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700 transition">
            ← 注文履歴へ
        </a>
    </div>

    {{-- 注文概要 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between flex-wrap gap-2">
            <div>
                <p class="text-xs text-gray-400 tracking-wider mb-0.5">注文番号</p>
                <p class="text-sm font-bold text-gray-800 tracking-wide">{{ $order->order_number }}</p>
            </div>
            <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $order->status_color }}">
                {{ $order->status_label }}
            </span>
        </div>
        <dl class="divide-y divide-gray-100 text-sm">
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">注文日時</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->created_at->format('Y年m月d日 H:i') }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">お支払い状況</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->payment_status_label }}</dd>
            </div>
        </dl>
    </div>

    {{-- 配送先情報 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">配送先情報</h2>
        </div>
        <dl class="divide-y divide-gray-100 text-sm">
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">氏名</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->recipient_name }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">郵便番号</dt>
                <dd class="col-span-2 text-gray-900">〒 {{ $order->postal_code }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">住所</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->prefecture }}{{ $order->address }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">電話番号</dt>
                <dd class="col-span-2 text-gray-900">{{ $order->phone_number }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">メールアドレス</dt>
                <dd class="col-span-2 text-gray-900 break-all">{{ $order->email }}</dd>
            </div>
        </dl>
    </div>

    {{-- 注文商品一覧 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">ご注文商品</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">商品名</th>
                    <th class="text-right px-6 py-3 font-medium text-gray-500 w-20 hidden sm:table-cell">単価</th>
                    <th class="text-center px-6 py-3 font-medium text-gray-500 w-16">数量</th>
                    <th class="text-right px-6 py-3 font-medium text-gray-500 w-24">小計</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($order->items as $item)
                    <tr>
                        <td class="px-6 py-3 text-gray-800">
                            {{ $item->product_name }}
                            {{-- スナップショット価格をモバイルで表示 --}}
                            <span class="block text-xs text-gray-400 sm:hidden">{{ $item->formatted_price }}</span>
                        </td>
                        <td class="px-6 py-3 text-right text-gray-500 hidden sm:table-cell">
                            {{ $item->formatted_price }}
                        </td>
                        <td class="px-6 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-3 text-right font-medium text-gray-800">
                            {{ $item->formatted_subtotal }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">合計（税込）</span>
            <span class="text-xl font-bold text-gray-900">{{ $order->formatted_total_price }}</span>
        </div>
    </div>

</div>
@endsection
