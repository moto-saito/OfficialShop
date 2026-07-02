@extends('layouts.app')

@section('title', config('app.name'))

@section('content')

{{-- ========================================
     Hero: 企業理念 + 会社写真
======================================== --}}
<section class="relative overflow-hidden bg-gray-900 text-white">

    {{-- 背景画像（会社写真プレースホルダー） --}}
    <div class="absolute inset-0">
        <div class="w-full h-full bg-gradient-to-br from-indigo-900 via-gray-900 to-gray-800"></div>
        {{-- 実際の会社写真があれば下記のように差し替え
        <img src="/images/company.jpg" alt="会社写真"
             class="w-full h-full object-cover opacity-30"> --}}
        <div class="absolute inset-0 bg-black/50"></div>
    </div>

    <div class="relative max-w-6xl mx-auto px-4 py-24 md:py-36 flex flex-col md:flex-row items-center gap-12">

        {{-- 企業理念テキスト --}}
        <div class="flex-1 text-center md:text-left">
            <p class="text-indigo-300 text-sm font-semibold uppercase tracking-widest mb-4">Our Philosophy</p>
            <h1 class="text-4xl md:text-5xl font-bold leading-tight mb-6">
                品質と誠実さで、<br class="hidden md:block">
                あなたの毎日に<br class="hidden md:block">
                彩りを。
            </h1>
            <p class="text-gray-300 text-base md:text-lg leading-relaxed max-w-md mx-auto md:mx-0 mb-8">
                私たちは「お客様の生活をより豊かに」という信念のもと、
                厳選された素材と丁寧な製造で、長く愛される商品をお届けします。
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start">
                <a href="{{ route('products.index') }}"
                   class="inline-block bg-indigo-600 hover:bg-indigo-500 text-white font-semibold px-8 py-3 rounded-xl transition">
                    商品を見る
                </a>
                <a href="{{ route('news.index') }}"
                   class="inline-block border border-white/30 hover:bg-white/10 text-white font-semibold px-8 py-3 rounded-xl transition">
                    お知らせ
                </a>
            </div>
        </div>

        {{-- 会社写真エリア（プレースホルダー） --}}
        <div class="flex-shrink-0 w-full md:w-80 lg:w-96">
            <div class="rounded-2xl overflow-hidden shadow-2xl aspect-[4/3] bg-gray-700 flex items-center justify-center border border-white/10">
                {{-- 実際の写真に差し替え可能 --}}
                <div class="text-center text-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-sm">会社写真</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ========================================
     お知らせ
======================================== --}}
<section class="py-16 md:py-20 bg-white">
    <div class="max-w-6xl mx-auto px-4">

        {{-- セクションヘッダー --}}
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-indigo-500 text-xs font-semibold uppercase tracking-widest mb-1">News</p>
                <h2 class="text-2xl font-bold text-gray-900">お知らせ</h2>
            </div>
            <a href="{{ route('news.index') }}"
               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition flex-shrink-0 ml-4">
                一覧を見る
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if ($latestNews->isEmpty())
            <p class="text-gray-400 text-center py-12">お知らせはありません。</p>
        @else
            <ul class="divide-y divide-gray-100 border-t border-gray-100">
                @foreach ($latestNews as $news)
                    <li>
                        <a href="{{ route('news.show', $news) }}"
                           class="flex items-center gap-4 py-4 hover:bg-gray-50 -mx-4 px-4 rounded-xl transition group">
                            <time class="text-xs text-gray-400 flex-shrink-0 w-24">
                                {{ $news->published_at->format('Y.m.d') }}
                            </time>
                            <span class="text-sm text-gray-700 group-hover:text-indigo-600 transition truncate">
                                {{ $news->title }}
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 flex-shrink-0 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6 text-center">
                <a href="{{ route('news.index') }}"
                   class="inline-flex items-center gap-2 border border-indigo-200 text-indigo-600 hover:bg-indigo-50 text-sm font-medium px-6 py-2.5 rounded-xl transition">
                    すべてのお知らせを見る
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
<section class="py-16 md:py-20 bg-gray-50">
    <div class="max-w-6xl mx-auto px-4">

        {{-- セクションヘッダー --}}
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="text-indigo-500 text-xs font-semibold uppercase tracking-widest mb-1">Products</p>
                <h2 class="text-2xl font-bold text-gray-900">おすすめ商品</h2>
            </div>
            <a href="{{ route('products.index') }}"
               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center gap-1 transition flex-shrink-0 ml-4">
                一覧を見る
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if ($products->isEmpty())
            <p class="text-gray-400 text-center py-12">現在販売中の商品はありません。</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow">

                        {{-- 商品画像 --}}
                        <a href="{{ route('products.show', $product) }}" class="block">
                            @if ($product->image_path)
                                <img src="{{ asset($product->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full aspect-square object-cover">
                            @else
                                <div class="w-full aspect-square bg-gray-100 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                        </a>

                        <div class="p-5 flex flex-col gap-3 flex-1">
                            <a href="{{ route('products.show', $product) }}"
                               class="font-semibold text-gray-800 hover:text-indigo-600 transition leading-snug line-clamp-2">
                                {{ $product->name }}
                            </a>
                            <p class="text-xl font-bold text-indigo-700">{{ $product->formatted_price }}</p>

                            {{-- カート追加 --}}
                            <form method="POST" action="{{ route('cart.store') }}" class="mt-auto">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <div class="flex items-center gap-2 mb-3">
                                    <button type="button" onclick="changeQty(this,-1)"
                                            class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                        &minus;
                                    </button>
                                    <input type="number" name="quantity" value="1" min="1" max="99"
                                           class="w-12 text-center border border-gray-300 rounded-lg py-1 text-sm font-medium">
                                    <button type="button" onclick="changeQty(this,1)"
                                            class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition">
                                        &#43;
                                    </button>
                                </div>
                                <button type="submit"
                                        class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">
                                    カートに追加
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="mt-10 text-center">
            <a href="{{ route('products.index') }}"
               class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold px-8 py-3 rounded-xl transition">
                商品一覧をすべて見る
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
