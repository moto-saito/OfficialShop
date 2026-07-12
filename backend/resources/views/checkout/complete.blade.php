@extends('layouts.app')

@section('title', '注文完了')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/checkout.css') }}">
@endpush

@section('content')
<div class="checkout_layout">

    @include('checkout._steps', ['step' => 3])

    <div class="checkout_complete_body">

        {{-- 完了アイコン --}}
        <div class="checkout_complete_icon-wrap">
            <svg class="checkout_complete_icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="checkout_complete_title">ご注文ありがとうございました</h1>
        <p class="checkout_complete_lead">ご注文を受け付けました。</p>

        {{-- 注文番号 --}}
        <div class="common_card common_card--bordered checkout_complete_order-card">
            <p class="checkout_complete_order-number-label">注文番号</p>
            <p class="checkout_complete_order-number">{{ $orderNumber }}</p>
        </div>

        <p class="checkout_complete_note">
            ご注文内容は<a href="{{ route('mypage.orders.index') }}" class="checkout_complete_note-link">注文履歴</a>からご確認いただけます。
        </p>

        <div class="checkout_complete_actions">
            <a href="{{ route('products.index') }}"
               class="checkout_complete_cta-primary">
                商品一覧へ戻る
            </a>
            <a href="{{ route('mypage.orders.index') }}"
               class="checkout_complete_cta-secondary">
                注文履歴を見る
            </a>
        </div>
    </div>

</div>
@endsection
