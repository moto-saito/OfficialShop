@extends('admin.layouts.app')

@section('title', 'ダッシュボード')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-dashboard.css') }}">
@endpush

@section('content')

{{-- サマリーカード --}}
<div class="admin_dashboard_summary-grid">
    <div class="admin_dashboard_summary-card">
        <p class="admin_dashboard_summary-label">総注文数</p>
        <p class="admin_dashboard_summary-value">{{ number_format($totalOrderCount) }}<span class="admin_dashboard_summary-unit">件</span></p>
    </div>
    <div class="admin_dashboard_summary-card">
        <p class="admin_dashboard_summary-label">本日の注文数</p>
        <p class="admin_dashboard_summary-value">{{ number_format($todayOrderCount) }}<span class="admin_dashboard_summary-unit">件</span></p>
    </div>
    <div class="admin_dashboard_summary-card">
        <p class="admin_dashboard_summary-label">売上合計</p>
        <p class="admin_dashboard_summary-value">¥{{ number_format($totalSales) }}</p>
    </div>
    <div class="admin_dashboard_summary-card">
        <p class="admin_dashboard_summary-label">未発送件数</p>
        <p class="admin_dashboard_summary-value">{{ number_format($unshippedCount) }}<span class="admin_dashboard_summary-unit">件</span></p>
    </div>
</div>

{{-- 最新注文5件 --}}
<div class="admin_card">
    <div class="admin_dashboard_orders-header">
        <h2 class="common_section-title">最新注文</h2>
        <a href="{{ route('admin.orders.index') }}" class="admin_dashboard_orders-link">すべて見る →</a>
    </div>
    <table class="common_table">
        <thead>
            <tr>
                <th>注文番号</th>
                <th class="admin_dashboard_col-date">注文日</th>
                <th>購入者氏名</th>
                <th class="admin_dashboard_col-amount">注文金額</th>
                <th class="admin_dashboard_col-status">ステータス</th>
                <th class="admin_dashboard_col-action"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($latestOrders as $order)
                <tr>
                    <td class="admin_dashboard_row-number">{{ $order->order_number }}</td>
                    <td class="admin_dashboard_row-date">{{ $order->created_at->format('Y/m/d H:i') }}</td>
                    <td class="admin_dashboard_row-recipient">{{ $order->recipient_name }}</td>
                    <td class="admin_dashboard_row-amount">{{ $order->formatted_total_price }}</td>
                    <td>
                        <span class="common_status-badge {{ $order->status_color }}">
                            {{ $order->status_label }}
                        </span>
                    </td>
                    <td class="admin_dashboard_row-action">
                        <a href="{{ route('admin.orders.show', $order) }}" class="common_button-outline--xs">
                            詳細
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin_dashboard_empty">注文がありません</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
