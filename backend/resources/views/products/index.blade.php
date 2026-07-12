@extends('layouts.app')

@section('title', '商品一覧')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/products-index.css') }}">
@endpush

@section('content')
<div class="products_index_layout">
    <h1 class="common_page-title">商品一覧</h1>

    <div class="products_index_grid-wrap">
        <div class="products_index_main-col">
    @if ($products->isEmpty())
        <p class="products_index_empty">現在販売中の商品はありません。</p>
    @else
        <div class="products_index_product-grid">
            @foreach ($products as $product)
                <div class="products_index_product-card">
                    {{-- 商品画像 --}}
                    <a href="{{ route('products.show', $product) }}" class="products_index_product-image-link">
                        @if ($product->image_path)
                            <img src="{{ asset($product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="products_index_product-image">
                        @else
                            <div class="products_index_product-image-placeholder">
                                <svg xmlns="http://www.w3.org/2000/svg" class="products_index_product-image-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </a>

                    <div class="products_index_product-body">
                        {{-- 商品名 --}}
                        <a href="{{ route('products.show', $product) }}"
                           class="products_index_product-name">
                            {{ $product->name }}
                        </a>

                        {{-- 商品価格 --}}
                        <p class="products_index_product-price">{{ $product->formatted_price }}</p>

                        {{-- カート追加フォーム --}}
                        <form method="POST" action="{{ route('cart.store') }}" class="products_index_add-cart-form js-add-to-cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            {{-- 個数増減 --}}
                            <div class="products_index_qty-control">
                                <button type="button"
                                        onclick="changeQty(this, -1)"
                                        class="common_quantity-button common_quantity-button--glyph">
                                    &minus;
                                </button>
                                <input type="number" name="quantity" value="1" min="1" max="99"
                                       class="common_quantity-input">
                                <button type="button"
                                        onclick="changeQty(this, 1)"
                                        class="common_quantity-button common_quantity-button--glyph">
                                    &#43;
                                </button>
                            </div>

                            {{-- カートに追加 --}}
                            @if ($product->isSoldOut())
                                <button type="button" disabled
                                        class="products_index_sold-out-button">
                                    売り切れ
                                </button>
                            @else
                                <button type="submit"
                                        class="products_index_add-cart-button">
                                    カートに追加
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ページネーション --}}
        <div class="products_index_pagination-wrap">
            {{ $products->links() }}
        </div>
    @endif
        </div>

        {{-- 小計欄 --}}
        <aside class="products_index_cart-summary">
            <div class="products_index_cart-summary-card">
                <h2 class="products_index_cart-summary-title">カート内容</h2>

                @php $cartSubtotal = $cartItems->sum(fn ($item) => $item->subtotal); @endphp

                <div id="cart-summary-items" class="products_index_cart-summary-items {{ $cartItems->isEmpty() ? 'hidden' : '' }}">
                    @foreach ($cartItems as $item)
                        <div class="products_index_cart-summary-item">
                            <p class="products_index_cart-summary-item-name">{{ $item->product->name ?? '（削除された商品）' }}</p>
                            <div class="products_index_cart-summary-item-row">
                                <span>{{ $item->quantity }}個 &times; {{ $item->product->formatted_price ?? '―' }}</span>
                                <span class="products_index_cart-summary-item-subtotal">¥{{ number_format($item->subtotal) }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <p id="cart-summary-empty" class="products_index_cart-summary-empty {{ $cartItems->isEmpty() ? '' : 'hidden' }}">
                    カートに商品がありません
                </p>

                <div class="products_index_cart-summary-total-row">
                    <span class="products_index_cart-summary-total-label">小計</span>
                    <span id="cart-summary-total" class="products_index_cart-summary-total">¥{{ number_format($cartSubtotal) }}</span>
                </div>

                <a id="cart-summary-checkout" href="{{ route('cart.index') }}"
                   class="products_index_checkout-button {{ $cartItems->isEmpty() ? 'is-disabled' : '' }}">
                    会計に進む
                </a>
            </div>
        </aside>
    </div>
</div>

<script>
function changeQty(btn, delta) {
    const input = btn.closest('form').querySelector('input[name="quantity"]');
    const next = parseInt(input.value) + delta;
    if (next >= 1 && next <= 99) input.value = next;
}

function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str ?? '';
    return div.innerHTML;
}

function updateCartSummary(data) {
    const itemsWrap = document.getElementById('cart-summary-items');
    const emptyMsg = document.getElementById('cart-summary-empty');
    const totalEl = document.getElementById('cart-summary-total');
    const checkoutBtn = document.getElementById('cart-summary-checkout');

    if (!data.items.length) {
        itemsWrap.innerHTML = '';
        itemsWrap.classList.add('hidden');
        emptyMsg.classList.remove('hidden');
        checkoutBtn.classList.add('is-disabled');
    } else {
        itemsWrap.innerHTML = data.items.map(item => `
            <div class="products_index_cart-summary-item">
                <p class="products_index_cart-summary-item-name">${escapeHtml(item.name)}</p>
                <div class="products_index_cart-summary-item-row">
                    <span>${item.quantity}個 &times; ¥${Number(item.price).toLocaleString()}</span>
                    <span class="products_index_cart-summary-item-subtotal">¥${Number(item.subtotal).toLocaleString()}</span>
                </div>
            </div>
        `).join('');
        itemsWrap.classList.remove('hidden');
        emptyMsg.classList.add('hidden');
        checkoutBtn.classList.remove('is-disabled');
    }

    totalEl.textContent = '¥' + Number(data.subtotal).toLocaleString();
}

document.querySelectorAll('.js-add-to-cart-form').forEach(form => {
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: new FormData(form),
        })
        .then(res => {
            if (!res.ok) throw new Error('カートへの追加に失敗しました。');
            return res.json();
        })
        .then(updateCartSummary)
        .catch(() => alert('カートへの追加に失敗しました。'));
    });
});
</script>
@endsection
