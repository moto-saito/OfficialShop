@extends('admin.layouts.app')

@section('title', '注文管理')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-orders.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.orders.export', array_filter($filters)) }}"
       class="admin_header-action-button--outline">
        CSV出力
    </a>
@endsection

@section('content')

{{-- 検索フォーム --}}
<div class="admin_card admin_orders_filter-card">
    <form method="GET" action="{{ route('admin.orders.index') }}">
        <div class="admin_orders_index_filter-grid">
            <div>
                <label class="admin_orders_filter-label">注文番号</label>
                <input type="text" name="order_number" value="{{ $filters['order_number'] ?? '' }}"
                       class="admin_orders_filter-input">
            </div>
            <div>
                <label class="admin_orders_filter-label">購入者氏名</label>
                <input type="text" name="recipient_name" value="{{ $filters['recipient_name'] ?? '' }}"
                       class="admin_orders_filter-input">
            </div>
            <div>
                <label class="admin_orders_filter-label">メールアドレス</label>
                <input type="text" name="email" value="{{ $filters['email'] ?? '' }}"
                       class="admin_orders_filter-input">
            </div>
            <div>
                <label class="admin_orders_filter-label">注文ステータス</label>
                <select name="status" class="admin_orders_filter-input">
                    <option value="">すべて</option>
                    @foreach (['pending' => '受付中', 'processing' => '準備中', 'shipped' => '発送済', 'completed' => '完了', 'cancelled' => 'キャンセル'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin_orders_filter-label">決済ステータス</label>
                <select name="payment_status" class="admin_orders_filter-input">
                    <option value="">すべて</option>
                    @foreach (['unpaid' => '未払い', 'paid' => '支払済'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['payment_status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="admin_orders_filter-label">注文日（期間指定）</label>
                <div class="admin_orders_index_date-range">
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                           class="admin_orders_filter-input">
                    <span class="admin_orders_index_date-range-sep">〜</span>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                           class="admin_orders_filter-input">
                </div>
            </div>
        </div>
        <div class="admin_orders_index_filter-actions">
            <button type="submit" class="common_button-primary--compact">
                検索
            </button>
            <a href="{{ route('admin.orders.index') }}" class="common_back-link">
                条件をクリア
            </a>
        </div>
    </form>
</div>

{{-- 注文一覧 --}}
<div class="admin_card">
    <div class="admin_orders_index_table-scroll">
        <table class="common_table">
            <thead>
                <tr>
                    <th>注文番号</th>
                    <th class="admin_orders_index_col-date">注文日</th>
                    <th>購入者氏名</th>
                    <th>メールアドレス</th>
                    <th class="admin_orders_index_col-amount">注文金額</th>
                    <th class="admin_orders_index_col-status">注文ステータス</th>
                    <th class="admin_orders_index_col-status">決済ステータス</th>
                    <th class="admin_orders_index_col-action"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="admin_orders_index_number-cell">{{ $order->order_number }}</td>
                        <td class="admin_orders_index_date-cell">{{ $order->created_at->format('Y/m/d H:i') }}</td>
                        <td class="admin_orders_index_recipient-cell">{{ $order->recipient_name }}</td>
                        <td class="admin_orders_index_email-cell">{{ $order->email }}</td>
                        <td class="admin_orders_index_amount-cell">{{ $order->formatted_total_price }}</td>
                        <td>
                            <span class="common_status-badge {{ $order->status_color }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td>
                            @if ($order->payment_status === 'paid')
                                <span class="common_status-badge common_status-badge--success">
                                    <span class="common_status-badge__dot"></span>支払済
                                </span>
                            @else
                                <span class="common_status-badge common_status-badge--neutral">
                                    <span class="common_status-badge__dot"></span>未払い
                                </span>
                            @endif
                        </td>
                        <td class="admin_orders_index_action-cell">
                            <a href="{{ route('admin.orders.show', $order) }}"
                               class="common_button-outline--xs">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="admin_orders_index_empty">
                            該当する注文がありません
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="admin_orders_index_pagination-wrap">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
