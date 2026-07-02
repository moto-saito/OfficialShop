@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 py-10">

    {{-- パンくず --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-2">
        <a href="/products" class="hover:text-indigo-600 transition">商品一覧</a>
        <span>/</span>
        <span class="text-gray-600">{{ $product->name }}</span>
    </nav>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col md:flex-row gap-0">

        {{-- 商品画像 --}}
        <div class="md:w-1/2 bg-gray-50">
            @if ($product->image_path)
                <img src="{{ asset($product->image_path) }}"
                     alt="{{ $product->name }}"
                     class="w-full aspect-square object-cover">
            @else
                <div class="w-full aspect-square flex items-center justify-center bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- 商品情報 --}}
        <div class="md:w-1/2 p-8 flex flex-col gap-5">

            {{-- 商品名 --}}
            <h1 class="text-2xl font-bold leading-snug">{{ $product->name }}</h1>

            {{-- 商品価格 --}}
            <p class="text-3xl font-bold text-indigo-700">{{ $product->formatted_price }}<span class="text-base font-normal text-gray-400 ml-1">（税込）</span></p>

            {{-- 商品説明 --}}
            @if ($product->description)
                <div class="text-sm text-gray-600 leading-relaxed border-t pt-4">
                    {{ $product->description }}
                </div>
            @endif

            {{-- カート追加フォーム --}}
            <form method="POST" action="{{ route('cart.store') }}" class="border-t pt-5 flex flex-col gap-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">

                {{-- 個数増減 --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">数量</label>
                    <div class="flex items-center gap-3">
                        <button type="button"
                                id="btn-minus"
                                class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition text-xl leading-none">
                            &minus;
                        </button>
                        <input type="number" name="quantity" id="qty" value="1" min="1" max="99"
                               class="w-16 text-center border border-gray-300 rounded-lg py-2 font-semibold text-base">
                        <button type="button"
                                id="btn-plus"
                                class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition text-xl leading-none">
                            &#43;
                        </button>
                    </div>
                </div>

                {{-- カートに追加 --}}
                @if ($product->isSoldOut())
                    <button type="button" disabled
                            class="w-full py-3 rounded-xl bg-gray-200 text-gray-400 font-semibold cursor-not-allowed">
                        売り切れ
                    </button>
                @else
                    <button type="submit"
                            class="w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold transition">
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
