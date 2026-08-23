@extends('layouts.app')

@section('title', 'おすすめレシピ')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/recipes-index.css') }}">
@endpush

@section('content')
<div class="recipes_index_layout">
    <h1 class="common_page-title">おすすめレシピ</h1>

    @if ($recipes->isEmpty())
        <p class="recipes_index_empty">レシピはありません。</p>
    @else
        <ul class="recipes_index_list">
            @foreach ($recipes as $recipe)
                <li>
                    <a href="{{ route('recipes.show', $recipe) }}"
                       class="recipes_index_item-link">

                        {{-- サムネイル --}}
                        <div class="recipes_index_thumb">
                            @if ($recipe->image_path)
                                <img src="{{ asset('storage/' . $recipe->image_path) }}"
                                     alt="{{ $recipe->title }}"
                                     class="recipes_index_thumb-image">
                            @else
                                <div class="recipes_index_thumb-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="recipes_index_thumb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h6"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- テキスト --}}
                        <div class="recipes_index_item-text">
                            <p class="recipes_index_item-date">
                                {{ $recipe->published_at->format('Y年n月j日') }}
                            </p>
                            <p class="recipes_index_item-title">
                                {{ $recipe->title }}
                            </p>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" class="recipes_index_item-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- ページネーション --}}
        <div class="recipes_index_pagination-wrap">
            {{ $recipes->links() }}
        </div>
    @endif
</div>
@endsection
