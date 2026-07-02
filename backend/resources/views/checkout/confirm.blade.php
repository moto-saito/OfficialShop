@extends('layouts.app')

@section('title', '注文内容の確認')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    @include('checkout._steps', ['step' => 2])

    <h1 class="text-2xl font-bold text-gray-900 mb-8">注文内容の確認</h1>

    {{-- 購入者情報 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">購入者情報</h2>
        </div>
        <dl class="divide-y divide-gray-100 text-sm">
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">氏名</dt>
                <dd class="col-span-2 text-gray-900">{{ $validated['recipient_name'] }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">郵便番号</dt>
                <dd class="col-span-2 text-gray-900">〒 {{ $validated['postal_code'] }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">住所</dt>
                <dd class="col-span-2 text-gray-900">{{ $validated['prefecture'] }}{{ $validated['address'] }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">電話番号</dt>
                <dd class="col-span-2 text-gray-900">{{ $validated['phone_number'] }}</dd>
            </div>
            <div class="px-6 py-3 grid grid-cols-3 gap-4">
                <dt class="text-gray-500">メールアドレス</dt>
                <dd class="col-span-2 text-gray-900 break-all">{{ $validated['email'] }}</dd>
            </div>
        </dl>
    </div>

    {{-- 注文商品一覧 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-700">ご注文商品</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-3 font-medium text-gray-500">商品名</th>
                    <th class="text-right px-6 py-3 font-medium text-gray-500 w-20">単価</th>
                    <th class="text-center px-6 py-3 font-medium text-gray-500 w-16">数量</th>
                    <th class="text-right px-6 py-3 font-medium text-gray-500 w-24">小計</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($items as $item)
                    <tr>
                        <td class="px-6 py-3 text-gray-800">
                            {{ $item->product ? $item->product->name : '（削除された商品）' }}
                        </td>
                        <td class="px-6 py-3 text-right text-gray-600">
                            {{ $item->product ? $item->product->formatted_price : '―' }}
                        </td>
                        <td class="px-6 py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-3 text-right font-medium text-gray-800">
                            ¥{{ number_format($item->subtotal) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
            <span class="text-sm font-semibold text-gray-700">合計（税込）</span>
            <span class="text-2xl font-bold text-gray-900">¥{{ number_format($totalPrice) }}</span>
        </div>
    </div>

    {{-- 注文確定フォーム --}}
    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf
        <div class="flex flex-col sm:flex-row justify-between gap-3">
            <a href="{{ route('checkout.index') }}"
               class="text-center px-6 py-3 rounded-xl border border-gray-300 text-sm text-gray-600 hover:bg-gray-100 transition">
                ← 入力へ戻る
            </a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">
                注文を確定する
            </button>
        </div>
    </form>

</div>
@endsection
