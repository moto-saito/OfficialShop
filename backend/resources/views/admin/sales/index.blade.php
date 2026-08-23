@extends('admin.layouts.app')

@section('title', '売上管理')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-sales.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.sales.export', array_filter($filters)) }}"
       class="admin_header-action-button--outline">
        CSV出力
    </a>
@endsection

@section('content')

{{-- 検索フォーム --}}
<div class="admin_card admin_sales_filter-card">
    <form method="GET" action="{{ route('admin.sales.index') }}">
        <div class="admin_sales_filter-grid">
            <div>
                <label class="common_admin-form-label">開始日</label>
                <input type="date" name="date_from"
                       value="{{ $filters['date_from'] ?? $from->format('Y-m-d') }}"
                       class="common_admin-form-input">
            </div>
            <div>
                <label class="common_admin-form-label">終了日</label>
                <input type="date" name="date_to"
                       value="{{ $filters['date_to'] ?? $to->format('Y-m-d') }}"
                       class="common_admin-form-input">
            </div>
            <div>
                <label class="common_admin-form-label">注文ステータス</label>
                <select name="status" class="common_admin-form-input">
                    <option value="">すべて</option>
                    @foreach (['pending' => '受付中', 'processing' => '準備中', 'shipped' => '発送済', 'completed' => '完了', 'cancelled' => 'キャンセル'] as $value => $label)
                        <option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="admin_sales_filter-actions">
            <button type="submit" class="common_button-primary--compact">
                検索
            </button>
            <a href="{{ route('admin.sales.index') }}" class="common_back-link">
                リセット
            </a>
        </div>
    </form>
</div>

{{-- 売上サマリー --}}
<div class="common_summary-grid admin_sales_summary-grid">
    <div class="common_summary-card">
        <p class="common_summary-label">売上合計（税込）</p>
        <p class="common_summary-value">¥{{ number_format($summary['gross_sales']) }}</p>
    </div>
    <div class="common_summary-card">
        <p class="common_summary-label">決済件数</p>
        <p class="common_summary-value">{{ number_format($summary['payment_count']) }}<span class="common_summary-unit">件</span></p>
    </div>
    <div class="common_summary-card">
        <p class="common_summary-label">注文件数</p>
        <p class="common_summary-value">{{ number_format($summary['order_count']) }}<span class="common_summary-unit">件</span></p>
    </div>
    <div class="common_summary-card">
        <p class="common_summary-label">返金額（{{ number_format($summary['refunded_count']) }}件）</p>
        <p class="common_summary-value common_summary-value--danger">¥{{ number_format($summary['refunded_amount']) }}</p>
    </div>
    <div class="common_summary-card">
        <p class="common_summary-label">純売上</p>
        <p class="common_summary-value common_summary-value--accent">¥{{ number_format($summary['net_sales']) }}</p>
    </div>
    <div class="common_summary-card common_summary-card--muted">
        <p class="common_summary-label">税額</p>
        <p class="common_summary-value">¥{{ number_format($summary['tax_amount']) }}</p>
    </div>
    <div class="common_summary-card common_summary-card--muted">
        <p class="common_summary-label">送料</p>
        <p class="common_summary-value">¥{{ number_format($summary['shipping_amount']) }}</p>
    </div>
    <div class="common_summary-card common_summary-card--muted">
        <p class="common_summary-label">割引額</p>
        <p class="common_summary-value">¥{{ number_format($summary['discount_amount']) }}</p>
    </div>
</div>

<p class="admin_sales_note">
    ※ 税額・送料・割引額は現在のシステムでは内訳を保持していないため0円で表示しています（売上合計は税込・送料込みの金額です）。<br>
    ※ 返金額は「支払済みかつキャンセル」となった注文の合計金額を近似的に集計したものです。
</p>

{{-- 決済一覧 --}}
<div class="admin_card">
    <div class="admin_sales_index_table-scroll">
        <table class="common_table">
            <thead>
                <tr>
                    <th>決済ID</th>
                    <th>注文ID</th>
                    <th class="admin_sales_index_col-date">決済日時</th>
                    <th>購入者</th>
                    <th>メールアドレス</th>
                    <th>商品・注文内容</th>
                    <th class="admin_sales_index_col-amount">支払総額</th>
                    <th class="admin_sales_index_col-method">決済方法</th>
                    <th class="admin_sales_index_col-status">決済ステータス</th>
                    <th class="admin_sales_index_col-status">注文ステータス</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ optional($order->paid_at)->format('Y/m/d H:i') }}</td>
                        <td>{{ $order->recipient_name }}</td>
                        <td class="admin_sales_index_email-cell">{{ $order->email }}</td>
                        <td class="admin_sales_index_items-cell">
                            {{ $order->items->map(fn ($item) => "{$item->product_name} ×{$item->quantity}")->implode('、') }}
                        </td>
                        <td class="admin_sales_index_amount-cell">{{ $order->formatted_total_price }}</td>
                        <td>{{ $order->payment_method_label }}</td>
                        <td>
                            <span class="common_status-badge common_status-badge--success">
                                {{ $order->payment_status_label }}
                            </span>
                        </td>
                        <td>
                            <span class="common_status-badge {{ $order->status_color }}">
                                {{ $order->status_label }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="admin_sales_index_empty">
                            指定期間内の決済データがありません
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($orders->hasPages())
        <div class="admin_sales_index_pagination-wrap">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
