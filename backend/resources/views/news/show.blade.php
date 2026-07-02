@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">

    {{-- パンくず --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-2">
        <a href="/news" class="hover:text-indigo-600 transition">お知らせ</a>
        <span>/</span>
        <span class="text-gray-600 truncate">{{ $news->title }}</span>
    </nav>

    <article class="bg-white rounded-2xl shadow-sm overflow-hidden">

        {{-- 画像 --}}
        @if ($news->image_path)
            <img src="{{ asset($news->image_path) }}"
                 alt="{{ $news->title }}"
                 class="w-full max-h-80 object-cover">
        @endif

        <div class="p-8">
            {{-- 投稿日 --}}
            <p class="text-sm text-gray-400 mb-3">
                {{ $news->published_at->format('Y年n月j日') }}
            </p>

            {{-- タイトル --}}
            <h1 class="text-2xl font-bold leading-snug mb-6">{{ $news->title }}</h1>

            {{-- 本文 --}}
            <div class="text-gray-700 leading-relaxed whitespace-pre-line">
                {{ $news->content }}
            </div>
        </div>
    </article>

    {{-- 一覧に戻る --}}
    <div class="mt-8">
        <a href="{{ route('news.index') }}"
           class="inline-flex items-center gap-2 text-sm text-indigo-600 hover:text-indigo-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            お知らせ一覧へ戻る
        </a>
    </div>

</div>
@endsection
