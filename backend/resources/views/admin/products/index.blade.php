@extends('admin.layouts.app')

@section('title', '商品管理')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-products.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.products.create') }}"
       class="admin_header-action-button">
        ＋ 新規商品追加
    </a>
@endsection

@section('content')
<div class="admin_card">
    <table class="common_table">
        <thead>
            <tr>
                <th class="admin_products_index_col-image">画像</th>
                <th>商品名</th>
                <th class="admin_products_index_col-price">価格</th>
                <th class="admin_products_index_col-status">販売状態</th>
                <th class="admin_products_index_col-action"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>

                    {{-- 画像 --}}
                    <td>
                        @if ($product->image_path)
                            <img src="{{ asset('storage/' . $product->image_path) }}"
                                 alt="{{ $product->name }}"
                                 class="admin_products_index_thumb">
                        @else
                            <div class="admin_products_index_thumb-placeholder">
                                <svg class="admin_products_index_thumb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </td>

                    {{-- 商品名 --}}
                    <td>
                        <p class="admin_products_index_name">{{ $product->name }}</p>
                    </td>

                    {{-- 価格 --}}
                    <td class="admin_products_index_price-cell">
                        {{ $product->formatted_price }}
                    </td>

                    {{-- 販売状態 --}}
                    <td>
                        @if ($product->status === 'selling')
                            <span class="common_status-badge common_status-badge--success">
                                <span class="common_status-badge__dot"></span>販売中
                            </span>
                        @elseif ($product->status === 'soldout')
                            <span class="common_status-badge common_status-badge--warning">
                                <span class="common_status-badge__dot"></span>売り切れ
                            </span>
                        @else
                            <span class="common_status-badge common_status-badge--neutral">
                                <span class="common_status-badge__dot"></span>非公開
                            </span>
                        @endif
                    </td>

                    {{-- 操作 --}}
                    <td>
                        <div class="admin_products_index_actions">

                            {{-- 編集 --}}
                            <a href="{{ route('admin.products.edit', $product) }}"
                               class="common_button-outline--xs">
                                編集
                            </a>

                            {{-- 削除 --}}
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}"
                                  onsubmit="return confirm('「{{ $product->name }}」を削除しますか？\nこの操作は取り消せません。')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="common_button-danger--xs">
                                    削除
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="admin_products_index_empty">
                        商品がありません
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($products->hasPages())
        <div class="admin_products_index_pagination-wrap">
            {{ $products->links() }}
        </div>
    @endif
</div>
@endsection
