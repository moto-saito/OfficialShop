@extends('layouts.app')

@section('title', '注文完了')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    @include('checkout._steps', ['step' => 3])

    <div class="text-center">

        {{-- 完了アイコン --}}
        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-6">
            <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">ご注文ありがとうございました</h1>
        <p class="text-gray-500 mb-8">ご注文を受け付けました。</p>

        {{-- 注文番号 --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-8 py-6 mb-8 inline-block w-full sm:w-auto sm:min-w-72">
            <p class="text-xs text-gray-400 uppercase tracking-wider mb-1">注文番号</p>
            <p class="text-xl font-bold text-indigo-600 tracking-widest">{{ $orderNumber }}</p>
        </div>

        <p class="text-sm text-gray-500 mb-10">
            ご注文内容は<a href="{{ route('mypage.orders.index') }}" class="text-indigo-600 hover:underline">注文履歴</a>からご確認いただけます。
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="{{ route('products.index') }}"
               class="w-full sm:w-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                商品一覧へ戻る
            </a>
            <a href="{{ route('mypage.orders.index') }}"
               class="w-full sm:w-auto text-center border border-gray-300 text-gray-600 hover:bg-gray-50 px-8 py-3 rounded-xl text-sm transition">
                注文履歴を見る
            </a>
        </div>
    </div>

</div>
@endsection
