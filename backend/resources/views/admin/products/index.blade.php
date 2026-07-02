@extends('admin.layouts.app')

@section('title', '商品管理')

@section('header-action')
    <a href="{{ route('admin.products.create') }}"
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
        ＋ 新規商品追加
    </a>
@endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-20">画像</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500">商品名</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-32">価格</th>
                <th class="text-left px-6 py-3 font-medium text-gray-500 w-28">販売状態</th>
                <th class="px-6 py-3 w-36"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($products as $product)
                <tr class="hover:bg-gray-50 transition">

                    {{-- 画像 --}}
                    <td class="px-6 py-4">
                        @if ($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="h-12 w-12 object-cover rounded-lg border">
                        @else
                            <div class="h-12 w-12 bg-gray-100 rounded-lg border flex items-center justify-center">
                                <svg class="h-5 w-5 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </td>

                    {{-- 商品名 --}}
                    <td class="px-6 py-4">
                        <p class="font-medium text-gray-800 truncate max-w-xs">{{ $product->name }}</p>
                    </td>

                    {{-- 価格 --}}
                    <td class="px-6 py-4 text-gray-700 font-medium">
                        {{ $product->formatted_price }}
                    </td>

                    {{-- 販売状態 --}}
                    <td class="px-6 py-4">
                        @if ($product->status === 'selling')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>販売中
                            </span>
                        @elseif ($product->status === 'soldout')
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>売り切れ
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>非公開
                            </span>
                        @endif
                    </td>

                    {{-- 操作 --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">

                            {{-- 編集 --}}
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="text-xs px-3 py-1.5 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition">
                                編集
                            </a>

                            {{-- 削除 --}}
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                  onsubmit="return confirm('「{{ $product->name }}」を削除しますか？\nこの操作は取り消せません。')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs px-3 py-1.5 rounded-lg border border-red-200 text-red-500 hover:bg-red-50 transition">
                                    削除
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-400">
                        商品がありません
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($products->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
