@extends('layouts.app')

@section('title', '注文内容の確認')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/checkout.css') }}">
@endpush

@section('content')
<div class="checkout_layout">

    @include('checkout._steps', ['step' => 2])

    <h1 class="checkout_title">注文内容の確認</h1>

    {{-- 購入者情報 --}}
    <div class="common_card common_card--bordered checkout_confirm_section-card">
        <div class="checkout_confirm_section-header">
            <h2 class="common_section-title">購入者情報</h2>
        </div>
        <dl class="common_info-list">
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">氏名</dt>
                <dd class="common_info-value">{{ $validated['recipient_name'] }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">郵便番号</dt>
                <dd class="common_info-value">〒 {{ $validated['postal_code'] }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">住所</dt>
                <dd class="common_info-value">{{ $validated['prefecture'] }}{{ $validated['address'] }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">電話番号</dt>
                <dd class="common_info-value">{{ $validated['phone_number'] }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">メールアドレス</dt>
                <dd class="common_info-value common_info-value--breakall">{{ $validated['email'] }}</dd>
            </div>
        </dl>
    </div>

    {{-- 注文商品一覧 --}}
    <div class="common_card common_card--bordered checkout_confirm_section-card">
        <div class="checkout_confirm_section-header">
            <h2 class="common_section-title">ご注文商品</h2>
        </div>
        <table class="common_table checkout_confirm_items-table">
            <thead>
                <tr>
                    <th>商品名</th>
                    <th class="checkout_confirm_col-price">単価</th>
                    <th class="checkout_confirm_col-qty">数量</th>
                    <th class="checkout_confirm_col-subtotal">小計</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            {{ $item->product ? $item->product->name : '（削除された商品）' }}
                        </td>
                        <td>
                            {{ $item->product ? $item->product->formatted_price : '―' }}
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td class="checkout_confirm_item-subtotal">
                            ¥{{ number_format($item->subtotal) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="checkout_confirm_total-row">
            <span class="checkout_confirm_total-label">合計（税込）</span>
            <span class="checkout_confirm_total-amount">¥{{ number_format($totalPrice) }}</span>
        </div>
    </div>

    {{-- 注文確定フォーム --}}
    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf
        <div class="checkout_actions">
            <a href="{{ route('checkout.index') }}"
               class="checkout_secondary-button">
                ← 入力へ戻る
            </a>
            <button type="submit"
                    class="checkout_primary-button">
                注文を確定する
            </button>
        </div>
    </form>

</div>
@endsection
