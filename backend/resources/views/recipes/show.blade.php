@extends('layouts.app')

@section('title', $recipe->title)

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/recipes-show.css') }}">
@endpush

@section('content')
<div class="recipes_show_layout">

    {{-- パンくず --}}
    <nav class="common_breadcrumb">
        <a href="{{ route('recipes.index') }}" class="common_breadcrumb-link">おすすめレシピ</a>
        <span>/</span>
        <span class="common_breadcrumb-current">{{ $recipe->title }}</span>
    </nav>

    {{-- メイン画像 --}}
    @if ($recipe->image_path)
        <img src="{{ asset('storage/' . $recipe->image_path) }}"
             alt="{{ $recipe->title }}"
             class="recipes_show_hero-image">
    @endif

    <div class="recipes_show_header">
        <h1 class="recipes_show_title">{{ $recipe->title }}</h1>

        <div class="recipes_show_meta">
            <p class="recipes_show_date">{{ $recipe->published_at->format('Y年n月j日') }}</p>
            @if ($recipe->servings)
                <span class="recipes_show_servings">
                    <svg xmlns="http://www.w3.org/2000/svg" class="recipes_show_servings-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-1a4 4 0 100-8 4 4 0 000 8zm6 3v2m-6-3a4 4 0 00-4 4v2m8-6a4 4 0 014 4v2"/>
                    </svg>
                    {{ $recipe->servings }}人分
                </span>
            @endif
        </div>

        @if ($recipe->content)
            <p class="recipes_show_intro">{{ $recipe->content }}</p>
        @endif
    </div>

    {{-- 材料 --}}
    @if ($recipe->ingredients->isNotEmpty())
        <section class="recipes_show_section">
            <h2 class="recipes_show_section-title">
                材料
                @if ($recipe->servings)
                    <span class="recipes_show_section-title-sub">（{{ $recipe->servings }}人分）</span>
                @endif
            </h2>
            <ul class="recipes_show_ingredient-list">
                @foreach ($recipe->ingredients as $ingredient)
                    <li class="recipes_show_ingredient-row">
                        <span class="recipes_show_ingredient-name">{{ $ingredient->name }}</span>
                        <span class="recipes_show_ingredient-leader"></span>
                        <span class="recipes_show_ingredient-amount">{{ $ingredient->amount }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- 作り方 --}}
    @if ($recipe->steps->isNotEmpty())
        <section class="recipes_show_section">
            <h2 class="recipes_show_section-title">作り方</h2>
            <ol class="recipes_show_step-list">
                @foreach ($recipe->steps as $index => $step)
                    <li class="recipes_show_step">
                        <div class="recipes_show_step-number">{{ $index + 1 }}</div>
                        <div class="recipes_show_step-body">
                            @if ($step->image_path)
                                <img src="{{ asset('storage/' . $step->image_path) }}"
                                     alt="手順{{ $index + 1 }}"
                                     class="recipes_show_step-image">
                            @endif
                            <p class="recipes_show_step-text">{{ $step->body }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    {{-- シェア --}}
    <div class="recipes_show_share-wrap">
        <x-share-x-button :text="$recipe->title" />
    </div>

    {{-- 一覧に戻る --}}
    <div class="recipes_show_back-wrap">
        <a href="{{ route('recipes.index') }}"
           class="recipes_show_back-link">
            <svg xmlns="http://www.w3.org/2000/svg" class="recipes_show_back-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            おすすめレシピ一覧へ戻る
        </a>
    </div>

</div>
@endsection
