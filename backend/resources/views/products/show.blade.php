@extends('layouts.app')

@section('title', $product->name)

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/products-show.css') }}">
@endpush

@section('content')
<div class="products_show_layout">

    {{-- パンくず --}}
    <nav class="common_breadcrumb">
        <a href="/products" class="common_breadcrumb-link">商品一覧</a>
        <span>/</span>
        <span class="common_breadcrumb-current">{{ $product->name }}</span>
    </nav>

    <div class="products_show_card">

        {{-- 商品画像 --}}
        <div class="products_show_image-wrap">
            @if ($product->image_path)
                <img src="{{ asset($product->image_path) }}"
                     alt="{{ $product->name }}"
                     class="products_show_image">
            @else
                <div class="products_show_image-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" class="products_show_image-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- 商品情報 --}}
        <div class="products_show_info">

            {{-- 商品名 --}}
            <h1 class="products_show_name">{{ $product->name }}</h1>

            {{-- 商品価格 --}}
            <p class="products_show_price">{{ $product->formatted_price }}<span class="products_show_price-tax">（税込）</span></p>

            {{-- 商品説明 --}}
            @if ($product->description)
                <div class="products_show_description">
                    {{ $product->description }}
                </div>
            @endif

            {{-- カート追加フォーム --}}
            <form method="POST" action="{{ route('cart.store') }}" class="products_show_form">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- 個数増減 --}}
                <div>
                    <label class="products_show_qty-label">数量</label>
                    <div class="products_show_qty-row">
                        <button type="button"
                                id="btn-minus"
                                class="common_quantity-button common_quantity-button--lg">
                            &minus;
                        </button>
                        <input type="number" name="quantity" id="qty" value="1" min="1" max="99"
                               class="common_quantity-input common_quantity-input--lg">
                        <button type="button"
                                id="btn-plus"
                                class="common_quantity-button common_quantity-button--lg">
                            &#43;
                        </button>
                    </div>
                </div>

                {{-- カートに追加 --}}
                @if ($product->isSoldOut())
                    <button type="button" disabled
                            class="products_show_sold-out-button">
                        売り切れ
                    </button>
                @else
                    <button type="submit"
                            class="products_show_add-cart-button">
                        カートに追加
                    </button>
                @endif
            </form>

        </div>
    </div>
</div>

<script>
const qty = document.getElementById('qty');
document.getElementById('btn-minus').addEventListener('click', () => {
    if (parseInt(qty.value) > 1) qty.value = parseInt(qty.value) - 1;
});
document.getElementById('btn-plus').addEventListener('click', () => {
    if (parseInt(qty.value) < 99) qty.value = parseInt(qty.value) + 1;
});
</script>
@endsection
