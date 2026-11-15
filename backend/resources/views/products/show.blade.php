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

        {{-- 商品画像（2枚以上はスライド表示、1枚はそのまま表示） --}}
        @php
            $imagePaths = $product->images->pluck('path');
            if ($imagePaths->isEmpty() && $product->image_path) {
                $imagePaths = collect([$product->image_path]);
            }
        @endphp
        <div class="products_show_image-wrap">
            @if ($imagePaths->count() > 1)
                <div class="products_show_slider" data-slider
                     role="region" aria-roledescription="carousel" aria-label="{{ $product->name }}の画像">
                    <div class="products_show_slider-track" data-slider-track tabindex="0">
                        @foreach ($imagePaths as $path)
                            <div class="products_show_slide" role="group" aria-roledescription="slide"
                                 aria-label="{{ $loop->iteration }} / {{ $loop->count }}">
                                <img src="{{ Storage::disk('public')->url($path) }}"
                                     alt="{{ $product->name }}（{{ $loop->iteration }}枚目）"
                                     class="products_show_image"
                                     @unless ($loop->first) loading="lazy" @endunless>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="products_show_slider-nav products_show_slider-nav--prev" data-slider-prev aria-label="前の画像">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    <button type="button" class="products_show_slider-nav products_show_slider-nav--next" data-slider-next aria-label="次の画像">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <div class="products_show_slider-dots">
                        @foreach ($imagePaths as $path)
                            <button type="button" class="products_show_slider-dot" data-slider-dot="{{ $loop->index }}"
                                    aria-label="{{ $loop->iteration }}枚目の画像を表示"
                                    @if ($loop->first) aria-current="true" @endif></button>
                        @endforeach
                    </div>
                </div>
                {{-- スライドがあるときだけスクリプトを読み込む --}}
@push('scripts')
<script>
// 商品画像のスライド（スワイプ・矢印ボタン・ドットで切り替え）
document.querySelectorAll('[data-slider]').forEach((slider) => {
    const track = slider.querySelector('[data-slider-track]');
    const slides = track.children;
    const dots = slider.querySelectorAll('[data-slider-dot]');
    const count = slides.length;
    const current = () => Math.round(track.scrollLeft / track.clientWidth);

    function go(index) {
        const i = (index + count) % count; // 最後の次は最初、最初の前は最後へ
        track.scrollTo({ left: i * track.clientWidth });
    }

    function updateDots() {
        const i = current();
        dots.forEach((dot, n) => dot.toggleAttribute('aria-current', n === i));
    }

    slider.querySelector('[data-slider-prev]').addEventListener('click', () => go(current() - 1));
    slider.querySelector('[data-slider-next]').addEventListener('click', () => go(current() + 1));
    dots.forEach((dot) => dot.addEventListener('click', () => go(Number(dot.dataset.sliderDot))));

    let ticking = false;
    track.addEventListener('scroll', () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => { updateDots(); ticking = false; });
    });

    // 左右キーで切り替え（スライド部分にフォーカスがあるとき）
    track.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') {
            e.preventDefault();
            go(current() + (e.key === 'ArrowRight' ? 1 : -1));
        }
    });

    // 画面幅が変わっても今の画像の位置を保つ
    let index = 0;
    track.addEventListener('scroll', () => { index = current(); }, { passive: true });
    window.addEventListener('resize', () => { track.scrollLeft = index * track.clientWidth; });
});
</script>
@endpush
            @elseif ($imagePaths->count() === 1)
                <img src="{{ Storage::disk('public')->url($imagePaths->first()) }}"
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

            {{-- シェア --}}
            <div class="products_show_share-wrap">
                <x-share-x-button :text="$product->name" />
            </div>

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

