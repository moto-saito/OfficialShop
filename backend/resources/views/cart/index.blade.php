@extends('layouts.app')

@section('title', 'カート')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/cart-index.css') }}">
@endpush

@section('content')
<div class="cart_index_layout">
    <h1 class="common_page-title">ショッピングカート</h1>

    @if (session('success'))
        <div class="common_flash-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($items->isEmpty())
        <div class="cart_index_empty">
            <svg xmlns="http://www.w3.org/2000/svg" class="cart_index_empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13l-1.4 7h12.8M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
            </svg>
            <p class="cart_index_empty-text">カートに商品がありません。</p>
            <a href="{{ route('products.index') }}"
               class="cart_index_empty-cta">
                商品一覧へ
            </a>
        </div>
    @else
        @php $total = $items->sum(fn($item) => $item->subtotal); @endphp

        <div class="cart_index_table-card">
            <table class="common_table">
                <thead>
                    <tr>
                        <th>商品</th>
                        <th class="cart_index_col-price">単価</th>
                        <th class="cart_index_col-qty">個数</th>
                        <th class="cart_index_col-subtotal">小計</th>
                        <th class="cart_index_col-action"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr>
                            {{-- 商品画像 + 名前 --}}
                            <td>
                                <div class="cart_index_product-cell">
                                    @if ($item->product && $item->product->image_path)
                                        <img src="{{ asset('storage/' . $item->product->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="cart_index_product-image">
                                    @else
                                        <div class="cart_index_product-image-placeholder">
                                            <svg class="cart_index_product-image-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="cart_index_product-name">
                                        {{ $item->product ? $item->product->name : '（削除された商品）' }}
                                    </span>
                                </div>
                            </td>

                            {{-- 単価 --}}
                            <td class="cart_index_price-cell">
                                {{ $item->product ? $item->product->formatted_price : '―' }}
                            </td>

                            {{-- 個数変更 --}}
                            <td>
                                <form method="POST" action="{{ route('cart.update', $item) }}" class="cart_index_qty-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                           class="cart_index_qty-input">
                                    <button type="submit"
                                            class="cart_index_qty-submit">
                                        変更
                                    </button>
                                </form>
                            </td>

                            {{-- 小計 --}}
                            <td class="cart_index_subtotal-cell">
                                ¥{{ number_format($item->subtotal) }}
                            </td>

                            {{-- 削除 --}}
                            <td class="cart_index_delete-cell">
                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('削除しますか？')"
                                            class="cart_index_delete-button" title="削除">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="cart_index_delete-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- 合計 --}}
            <div class="cart_index_total-row">
                <span class="cart_index_total-label">合計（税込）</span>
                <span class="cart_index_total-amount">¥{{ number_format($total) }}</span>
            </div>
        </div>

        <div class="cart_index_actions">
            <a href="{{ route('products.index') }}"
               class="cart_index_continue-link">
                <svg xmlns="http://www.w3.org/2000/svg" class="cart_index_continue-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                買い物を続ける
            </a>

            @auth
                <a href="{{ route('checkout.index') }}"
                   class="common_button-primary cart_index_checkout-cta">
                    ご購入手続きへ →
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="common_button-primary cart_index_checkout-cta">
                    ログインして購入する →
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection
