@extends('layouts.app')

@section('title', config('app.name'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/home.css') }}">
@endpush

@section('content')

{{-- ========================================
     Hero: TOPメインビジュアル
======================================== --}}
<section class="home_hero">
    <div class="home_hero-inner">
        <div class="home_hero-text">
            <h1 class="home_hero-title">
                品質と誠実さで、<br class="home_hero-break">
                あなたの毎日に<br class="home_hero-break">
                彩りを。
            </h1>
            <div class="home_hero-actions">
                <a href="{{ route('products.index') }}"
                   class="home_hero-cta-primary">
                    商品を見る
                </a>
                <a href="{{ route('news.index') }}"
                   class="home_hero-cta-secondary">
                    お知らせ
                </a>
            </div>
        </div>
    </div>
</section>

{{-- ========================================
     企業理念
======================================== --}}
<section class="home_section home_section--philosophy">
    <div class="home_container home_philosophy-inner">

        {{-- 企業理念テキスト --}}
        <div class="home_philosophy-text">
            <p class="home_section-eyebrow">Our Philosophy</p>
            <h2 class="home_section-title">企業理念</h2>
            <p class="home_philosophy-desc">
                私たちは「お客様の生活をより豊かに」という信念のもと、
                厳選された素材と丁寧な製造で、長く愛される商品をお届けします。
            </p>
        </div>

        {{-- 会社写真エリア（プレースホルダー） --}}
        <div class="home_philosophy-photo">
            <div class="home_philosophy-photo-frame">
                {{-- 実際の写真に差し替え可能 --}}
                <div class="home_philosophy-photo-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" class="home_philosophy-photo-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="home_philosophy-photo-caption">会社写真</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================
     お知らせ
======================================== --}}
<section class="home_section home_section--news">
    <div class="home_container">

        {{-- セクションヘッダー --}}
        <div class="home_section-header">
            <div>
                <p class="home_section-eyebrow">News</p>
                <h2 class="home_section-title">お知らせ</h2>
            </div>
            <a href="{{ route('news.index') }}"
               class="home_section-link">
                一覧を見る
                <svg xmlns="http://www.w3.org/2000/svg" class="home_icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if ($latestNews->isEmpty())
            <p class="home_empty-text">お知らせはありません。</p>
        @else
            <ul class="home_news-list">
                @foreach ($latestNews as $news)
                    <li>
                        <a href="{{ route('news.show', $news) }}"
                           class="home_news-item-link">
                            <time class="home_news-date">
                                {{ $news->published_at->format('Y.m.d') }}
                            </time>
                            <span class="home_news-title">
                                {{ $news->title }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="home_news-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="home_news-more-wrap">
                <a href="{{ route('news.index') }}"
                   class="home_news-more-link">
                    すべてのお知らせを見る
                    <svg xmlns="http://www.w3.org/2000/svg" class="home_icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
</section>

{{-- ========================================
     商品
======================================== --}}
<section class="home_section home_section--products">
    <div class="home_container">

        {{-- セクションヘッダー --}}
        <div class="home_section-header">
            <div>
                <p class="home_section-eyebrow">Products</p>
                <h2 class="home_section-title">おすすめ商品</h2>
            </div>
            <a href="{{ route('products.index') }}"
               class="home_section-link">
                一覧を見る
                <svg xmlns="http://www.w3.org/2000/svg" class="home_icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if ($products->isEmpty())
            <p class="home_empty-text">現在販売中の商品はありません。</p>
        @else
            <div class="home_products-grid">
                @foreach ($products as $product)
                    <div class="home_product-card">

                        {{-- 商品画像 --}}
                        <a href="{{ route('products.show', $product) }}" class="home_product-image-link">
                            @if ($product->image_path)
                                <img src="{{ asset($product->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="home_product-image">
                            @else
                                <div class="home_product-image-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="home_product-image-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <div class="home_product-body">
                            <a href="{{ route('products.show', $product) }}"
                               class="home_product-name">
                                {{ $product->name }}
                            </a>
                            <p class="home_product-price">{{ $product->formatted_price }}</p>

                            {{-- カート追加 --}}
                            <form method="POST" action="{{ route('cart.store') }}" class="home_product-form">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="home_qty-control">
                                    <button type="button" onclick="changeQty(this,-1)"
                                            class="common_quantity-button">
                                        &minus;
                                    </button>
                                    <input type="number" name="quantity" value="1" min="1" max="99"
                                           class="common_quantity-input">
                                    <button type="button" onclick="changeQty(this,1)"
                                            class="common_quantity-button">
                                        &#43;
                                    </button>
                                </div>
                                <button type="submit"
                                        class="home_add-cart-button">
                                    カートに追加
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="home_products-more-wrap">
            <a href="{{ route('products.index') }}"
               class="home_products-more-link">
                商品一覧をすべて見る
                <svg xmlns="http://www.w3.org/2000/svg" class="home_icon-sm" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function changeQty(btn, delta) {
    const input = btn.closest('form').querySelector('input[name="quantity"]');
    const next = parseInt(input.value) + delta;
    if (next >= 1 && next <= 99) input.value = next;
}
</script>
@endpush
