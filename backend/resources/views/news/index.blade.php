@extends('layouts.app')

@section('title', 'お知らせ一覧')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-10">
    <h1 class="text-2xl font-bold mb-8">お知らせ</h1>

    @if ($newsList->isEmpty())
        <p class="text-gray-500 text-center py-20">お知らせはありません。</p>
    @else
        <ul class="divide-y divide-gray-200">
            @foreach ($newsList as $news)
                <li>
                    <a href="{{ route('news.show', $news) }}"
                       class="flex items-center gap-4 py-5 hover:bg-gray-50 -mx-4 px-4 transition rounded-xl group">

                        {{-- サムネイル --}}
                        <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                            @if ($news->image_path)
                                <img src="{{ asset($news->image_path) }}"
                                     alt="{{ $news->title }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 12h6m-6-4h6"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- テキスト --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-gray-400 mb-1">
                                {{ $news->published_at->format('Y年n月j日') }}
                            </p>
                            <p class="font-semibold text-gray-800 group-hover:text-indigo-600 transition truncate">
                                {{ $news->title }}
                            </p>
                        </div>

                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </li>
            @endforeach
        </ul>

        {{-- ページネーション --}}
        <div class="mt-10">
            {{ $newsList->links() }}
        </div>
    @endif
</div>
@endsection
