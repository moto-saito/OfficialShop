@extends('layouts.app')

@section('title', '注文詳細')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/mypage.css') }}">
@endpush

@section('content')
<div class="mypage_layout">

    <div class="common_page-header">
        <h1 class="common_page-header-title">注文詳細</h1>
        <a href="{{ route('mypage.orders.index') }}" class="common_back-link">
            ← 注文履歴へ
        </a>
    </div>

    {{-- 注文概要 --}}
    <div class="common_card common_card--bordered mypage_section-card">
        <div class="mypage_orders_show_summary-header">
            <div>
                <p class="mypage_orders_show_order-number-label">注文番号</p>
                <p class="mypage_orders_show_order-number">{{ $order->order_number }}</p>
            </div>
            <span class="common_status-badge {{ $order->status_color }}">
                {{ $order->status_label }}
            </span>
        </div>
        <dl class="common_info-list">
            <div class="common_info-row">
                <dt class="common_info-label">注文日時</dt>
                <dd class="common_info-value">{{ $order->created_at->format('Y年m月d日 H:i') }}</dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label">お支払い状況</dt>
                <dd class="common_info-value">{{ $order->payment_status_label }}</dd>
            </div>
        </dl>
    </div>

    {{-- 配送先情報 --}}
    <div class="common_card common_card--bordered mypage_section-card">
        <div class="mypage_section-header">
            <h2 class="common_section-title">配送先情報</h2>
        </div>
        <dl class="common_info-list">
            <div class="common_info-row">
                <dt class="common_info-label">氏名</dt>
                <dd class="common_info-value">{{ $order->recipient_name }}</dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label">郵便番号</dt>
                <dd class="common_info-value">〒 {{ $order->postal_code }}</dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label">住所</dt>
                <dd class="common_info-value">{{ $order->prefecture }}{{ $order->address }}</dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label">電話番号</dt>
                <dd class="common_info-value">{{ $order->phone_number }}</dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label">メールアドレス</dt>
                <dd class="common_info-value common_info-value--breakall">{{ $order->email }}</dd>
            </div>
        </dl>
    </div>

    {{-- 注文商品一覧 --}}
    <div class="common_card common_card--bordered">
        <div class="mypage_section-header">
            <h2 class="common_section-title">ご注文商品</h2>
        </div>
        <table class="common_table mypage_orders_show_items-table">
            <thead>
                <tr>
                    <th>商品名</th>
                    <th class="mypage_orders_show_col-price">単価</th>
                    <th class="mypage_orders_show_col-qty">数量</th>
                    <th class="mypage_orders_show_col-subtotal">小計</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->product_name }}
                            {{-- スナップショット価格をモバイルで表示 --}}
                            <span class="mypage_orders_show_price-mobile">{{ $item->formatted_price }}</span>
                        </td>
                        <td class="mypage_orders_show_col-price">
                            {{ $item->formatted_price }}
                        </td>
                        <td>{{ $item->quantity }}</td>
                        <td class="mypage_orders_show_item-subtotal">
                            {{ $item->formatted_subtotal }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mypage_orders_show_total-row">
            <span class="mypage_orders_show_total-label">合計（税込）</span>
            <span class="mypage_orders_show_total-amount">{{ $order->formatted_total_price }}</span>
        </div>
    </div>

</div>
@endsection
