@extends('admin.layouts.app')

@section('title', '注文詳細')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-orders.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.orders.index') }}" class="common_back-link">
        ← 注文一覧へ戻る
    </a>
@endsection

@section('content')
<div class="admin_orders_show_layout">

    {{-- 注文情報 --}}
    <div class="admin_card admin_orders_show_section">
        <div class="admin_orders_show_summary-header">
            <div>
                <p class="admin_orders_show_order-number-label">注文番号</p>
                <p class="admin_orders_show_order-number">{{ $order->order_number }}</p>
            </div>
            <div class="admin_orders_show_badges">
                <span class="common_status-badge {{ $order->status_color }}">
                    {{ $order->status_label }}
                </span>
                @if ($order->payment_status === 'paid')
                    <span class="common_status-badge common_status-badge--success">
                        支払済
                    </span>
                @else
                    <span class="common_status-badge common_status-badge--neutral">
                        未払い
                    </span>
                @endif
            </div>
        </div>
        <dl class="common_info-list">
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">注文日</dt>
                <dd class="common_info-value">{{ $order->created_at->format('Y年m月d日 H:i') }}</dd>
            </div>
        </dl>
    </div>

    {{-- 購入者情報 --}}
    <div class="admin_card admin_orders_show_section">
        <div class="admin_card-header">
            <h2 class="common_section-title">購入者情報</h2>
        </div>
        <dl class="common_info-list">
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">氏名</dt>
                <dd class="common_info-value">{{ $order->recipient_name }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">郵便番号</dt>
                <dd class="common_info-value">〒 {{ $order->postal_code }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">都道府県</dt>
                <dd class="common_info-value">{{ $order->prefecture }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">住所</dt>
                <dd class="common_info-value">{{ $order->address }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">電話番号</dt>
                <dd class="common_info-value">{{ $order->phone_number }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">メールアドレス</dt>
                <dd class="common_info-value common_info-value--breakall">{{ $order->email }}</dd>
            </div>
        </dl>
    </div>

    {{-- 商品一覧 --}}
    <div class="admin_card admin_orders_show_section">
        <div class="admin_card-header">
            <h2 class="common_section-title">商品一覧</h2>
        </div>
        <table class="common_table admin_orders_show_items-table">
            <thead>
                <tr>
                    <th class="admin_orders_show_col-image">画像</th>
                    <th>商品名</th>
                    <th class="admin_orders_show_col-price">単価</th>
                    <th class="admin_orders_show_col-qty">数量</th>
                    <th class="admin_orders_show_col-subtotal">小計</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>
                            @if ($item->product?->image_path)
                                <img src="{{ asset('storage/' . $item->product->image_path) }}"
                                     alt="{{ $item->product_name }}"
                                     class="admin_orders_show_thumb">
                            @else
                                <div class="admin_orders_show_thumb-placeholder">
                                    <svg class="admin_orders_show_thumb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->formatted_price }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="admin_orders_show_item-subtotal">{{ $item->formatted_subtotal }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="admin_orders_show_total-row">
            <span class="admin_orders_show_total-label">合計金額</span>
            <span class="admin_orders_show_total-amount">{{ $order->formatted_total_price }}</span>
        </div>
    </div>

    {{-- ステータス変更 --}}
    <div class="admin_card admin_orders_show_section">
        <div class="admin_card-header">
            <h2 class="common_section-title">ステータス変更</h2>
        </div>
        <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}" class="admin_orders_show_status-form">
            @csrf
            @method('PATCH')
            <div class="admin_orders_show_status-grid">
                <div>
                    <label class="admin_orders_filter-label">注文ステータス</label>
                    <select name="status" class="admin_orders_filter-input">
                        @foreach (['pending' => '受付', 'processing' => '準備中', 'shipped' => '発送済', 'completed' => '完了', 'cancelled' => 'キャンセル'] as $value => $label)
                            <option value="{{ $value }}" @selected($order->status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="admin_orders_filter-label">決済ステータス</label>
                    <select name="payment_status" class="admin_orders_filter-input">
                        @foreach (['unpaid' => '未払い', 'paid' => '支払済'] as $value => $label)
                            <option value="{{ $value }}" @selected($order->payment_status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="common_button-primary--compact">
                更新する
            </button>
        </form>
    </div>

    {{-- 注文削除 --}}
    <div class="admin_card">
        <div class="admin_card-header">
            <h2 class="common_section-title">注文削除</h2>
        </div>
        <div class="admin_orders_show_danger-body">
            <p class="admin_orders_show_danger-note">誤登録などの理由で注文を削除する場合はこちらから削除できます。この操作は取り消せません。</p>
            <form method="POST" action="{{ route('admin.orders.destroy', $order) }}"
                  onsubmit="return confirm('注文番号「{{ $order->order_number }}」を削除しますか？\nこの操作は取り消せません。')">
                @csrf
                @method('DELETE')
                <button type="submit" class="admin_orders_show_danger-button">
                    この注文を削除する
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
