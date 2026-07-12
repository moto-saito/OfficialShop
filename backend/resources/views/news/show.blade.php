@extends('layouts.app')

@section('title', $news->title)

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/news-show.css') }}">
@endpush

@section('content')
<div class="news_show_layout">

    {{-- パンくず --}}
    <nav class="common_breadcrumb">
        <a href="/news" class="common_breadcrumb-link">お知らせ</a>
        <span>/</span>
        <span class="common_breadcrumb-current">{{ $news->title }}</span>
    </nav>

    <article class="common_card">

        {{-- 画像 --}}
        @if ($news->image_path)
            <img src="{{ asset($news->image_path) }}"
                 alt="{{ $news->title }}"
                 class="news_show_image">
        @endif

        <div class="news_show_body">
            {{-- 投稿日 --}}
            <p class="news_show_date">
                {{ $news->published_at->format('Y年n月j日') }}
            </p>

            {{-- タイトル --}}
            <h1 class="news_show_title">{{ $news->title }}</h1>

            {{-- 本文 --}}
            <div class="news_show_content">
                {{ $news->content }}
            </div>
        </div>
    </article>

    {{-- 一覧に戻る --}}
    <div class="news_show_back-wrap">
        <a href="{{ route('news.index') }}"
           class="news_show_back-link">
            <svg xmlns="http://www.w3.org/2000/svg" class="news_show_back-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            お知らせ一覧へ戻る
        </a>
    </div>

</div>
@endsection
