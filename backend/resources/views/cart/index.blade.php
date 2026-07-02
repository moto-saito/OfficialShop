@extends('layouts.app')

@section('title', 'カート')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-8">ショッピングカート</h1>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if ($items->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-10H5.4M7 13l-1.4 7h12.8M10 21a1 1 0 100-2 1 1 0 000 2zm8 0a1 1 0 100-2 1 1 0 000 2z"/>
            </svg>
            <p class="mb-4">カートに商品がありません。</p>
            <a href="{{ route('products.index') }}"
               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-6 py-2.5 rounded-xl transition">
                商品一覧へ
            </a>
        </div>
    @else
        @php $total = $items->sum(fn($item) => $item->subtotal); @endphp

        <div class="bg-white rounded-2xl shadow-sm overflow-hidden mb-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-6 py-3 font-medium text-gray-500">商品</th>
                        <th class="text-right px-6 py-3 font-medium text-gray-500 w-24">単価</th>
                        <th class="text-center px-6 py-3 font-medium text-gray-500 w-36">個数</th>
                        <th class="text-right px-6 py-3 font-medium text-gray-500 w-24">小計</th>
                        <th class="px-4 py-3 w-16"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($items as $item)
                        <tr class="hover:bg-gray-50 transition">
                            {{-- 商品画像 + 名前 --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($item->product && $item->product->image_path)
                                        <img src="{{ asset('storage/' . $item->product->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="h-14 w-14 object-cover rounded-lg border flex-shrink-0">
                                    @else
                                        <div class="h-14 w-14 bg-gray-100 rounded-lg border flex items-center justify-center flex-shrink-0">
                                            <svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <span class="font-medium text-gray-800">
                                        {{ $item->product ? $item->product->name : '（削除された商品）' }}
                                    </span>
                                </div>
                            </td>

                            {{-- 単価 --}}
                            <td class="px-6 py-4 text-right text-gray-700">
                                {{ $item->product ? $item->product->formatted_price : '―' }}
                            </td>

                            {{-- 個数変更 --}}
                            <td class="px-6 py-4">
                                <form method="POST" action="{{ route('cart.update', $item) }}" class="flex items-center justify-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                           class="w-16 text-center border border-gray-300 rounded-lg py-1.5 text-sm">
                                    <button type="submit"
                                            class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-medium rounded-lg transition">
                                        変更
                                    </button>
                                </form>
                            </td>

                            {{-- 小計 --}}
                            <td class="px-6 py-4 text-right font-medium text-gray-800">
                                ¥{{ number_format($item->subtotal) }}
                            </td>

                            {{-- 削除 --}}
                            <td class="px-4 py-4 text-center">
                                <form method="POST" action="{{ route('cart.destroy', $item) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            onclick="return confirm('削除しますか？')"
                                            class="text-gray-400 hover:text-red-500 transition" title="削除">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
            <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                <span class="text-sm text-gray-500">合計（税込）</span>
                <span class="text-xl font-bold text-gray-900">¥{{ number_format($total) }}</span>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('products.index') }}"
               class="text-sm text-indigo-600 hover:text-indigo-800 transition flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                買い物を続ける
            </a>

            @auth
                <a href="{{ route('checkout.index') }}"
                   class="w-full sm:w-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                    ご購入手続きへ →
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto text-center bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-xl transition">
                    ログインして購入する →
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection
