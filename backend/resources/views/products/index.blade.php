@extends('layouts.app')

@section('title', '商品一覧')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-8">商品一覧</h1>

    @if ($products->isEmpty())
        <p class="text-gray-500 text-center py-20">現在販売中の商品はありません。</p>
    @else
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($products as $product)
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    {{-- 商品画像 --}}
                    <a href="{{ route('products.show', $product) }}" class="block">
                        @if ($product->image_path)
                            <img src="{{ asset($product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="w-full aspect-square object-cover">
                        @else
                            <div class="w-full aspect-square bg-gray-100 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </a>

                    <div class="p-4 flex flex-col gap-3 flex-1">
                        {{-- 商品名 --}}
                        <a href="{{ route('products.show', $product) }}"
                           class="font-semibold text-sm leading-snug hover:text-indigo-600 transition line-clamp-2">
                            {{ $product->name }}
                        </a>

                        {{-- 商品価格 --}}
                        <p class="text-lg font-bold text-indigo-700">{{ $product->formatted_price }}</p>

                        {{-- カート追加フォーム --}}
                        <form method="POST" action="{{ route('cart.store') }}" class="mt-auto">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            {{-- 個数増減 --}}
                            <div class="flex items-center justify-center gap-2 mb-3">
                                <button type="button"
                                        onclick="changeQty(this, -1)"
                                        class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition text-lg leading-none">
                                    &minus;
                                </button>
                                <input type="number" name="quantity" value="1" min="1" max="99"
                                       class="w-12 text-center border border-gray-300 rounded-lg py-1 text-sm font-medium">
                                <button type="button"
                                        onclick="changeQty(this, 1)"
                                        class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition text-lg leading-none">
                                    &#43;
                                </button>
                            </div>

                            {{-- カートに追加 --}}
                            @if ($product->isSoldOut())
                                <button type="button" disabled
                                        class="w-full py-2 rounded-xl bg-gray-200 text-gray-400 text-sm font-semibold cursor-not-allowed">
                                    売り切れ
                                </button>
                            @else
                                <button type="submit"
                                        class="w-full py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">
                                    カートに追加
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ページネーション --}}
        <div class="mt-10">
            {{ $products->links() }}
        </div>
    @endif
</div>

<script>
function changeQty(btn, delta) {
    const input = btn.closest('form').querySelector('input[name="quantity"]');
    const next = parseInt(input.value) + delta;
    if (next >= 1 && next <= 99) input.value = next;
}
</script>
@endsection
