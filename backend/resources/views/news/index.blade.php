@extends('layouts.app')

@section('title', 'お知らせ一覧')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/news-index.css') }}">
@endpush

@section('content')
<div class="news_index_layout">
    <h1 class="common_page-title">お知らせ</h1>

    @if ($newsList->isEmpty())
        <p class="news_index_empty">お知らせはありません。</p>
    @else
        <ul class="news_index_list">
            @foreach ($newsList as $news)
                <li>
                    <a href="{{ route('news.show', $news) }}"
                       class="news_index_item-link">

                        {{-- サムネイル --}}
                        <div class="news_index_thumb">
                            @if ($news->image_path)
                                <img src="{{ asset($news->image_path) }}"
                                     alt="{{ $news->title }}"
                                     class="news_index_thumb-image">
                            @else
                                <div class="news_index_thumb-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="news_index_thumb-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h6"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- テキスト --}}
                        <div class="news_index_item-text">
                            <p class="news_index_item-date">
                                {{ $news->published_at->format('Y年n月j日') }}
                            </p>
                            <p class="news_index_item-title">
                                {{ $news->title }}
                            </p>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" class="news_index_item-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- ページネーション --}}
        <div class="news_index_pagination-wrap">
            {{ $newsList->links() }}
        </div>
    @endif
</div>
@endsection
