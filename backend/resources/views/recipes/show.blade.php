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

    <article class="common_card">

        {{-- 画像 --}}
        @if ($recipe->image_path)
            <img src="{{ asset($recipe->image_path) }}"
                 alt="{{ $recipe->title }}"
                 class="recipes_show_image">
        @endif

        <div class="recipes_show_body">
            {{-- 投稿日 --}}
            <p class="recipes_show_date">
                {{ $recipe->published_at->format('Y年n月j日') }}
            </p>

            {{-- タイトル --}}
            <h1 class="recipes_show_title">{{ $recipe->title }}</h1>

            {{-- 本文 --}}
            <div class="recipes_show_content">
                {{ $recipe->content }}
            </div>
        </div>
    </article>

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
