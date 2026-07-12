@extends('layouts.app')

@section('title', '注文履歴')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/mypage.css') }}">
@endpush

@section('content')
<div class="mypage_layout mypage_layout--wide">

    <div class="common_page-header">
        <h1 class="common_page-header-title">注文履歴</h1>
        <a href="{{ route('mypage.index') }}" class="common_back-link">
            ← マイページへ
        </a>
    </div>

    @if ($orders->isEmpty())
        <div class="mypage_orders_index_empty">
            <p class="mypage_orders_index_empty-text">まだ注文がありません。</p>
            <a href="{{ route('products.index') }}"
               class="mypage_orders_index_empty-cta">
                商品一覧へ
            </a>
        </div>
    @else
        <div class="common_card common_card--bordered">
            <table class="common_table">
                <thead>
                    <tr>
                        <th>注文番号</th>
                        <th class="mypage_orders_index_col-date">注文日</th>
                        <th class="mypage_orders_index_col-amount">金額</th>
                        <th class="mypage_orders_index_col-status">状態</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('mypage.orders.show', $order) }}"
                                   class="mypage_orders_index_number-link">
                                    {{ $order->order_number }}
                                </a>
                                {{-- モバイル用注文日 --}}
                                <p class="mypage_orders_index_date-mobile">
                                    {{ $order->created_at->format('Y/m/d') }}
                                </p>
                            </td>
                            <td class="mypage_orders_index_col-date">
                                {{ $order->created_at->format('Y/m/d H:i') }}
                            </td>
                            <td class="mypage_orders_index_amount-cell">
                                {{ $order->formatted_total_price }}
                            </td>
                            <td class="mypage_orders_index_status-cell">
                                <span class="common_status-badge {{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if ($orders->hasPages())
                <div class="mypage_orders_index_pagination-wrap">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    @endif

</div>
@endsection
