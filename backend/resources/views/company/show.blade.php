@extends('layouts.app')

@section('title', $company->title)

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/company-show.css') }}">
@endpush

@section('content')
<div class="company_show_layout">

    {{-- パンくず --}}
    <nav class="common_breadcrumb">
        <span class="common_breadcrumb-current">{{ $company->title }}</span>
    </nav>

    {{-- メイン画像 --}}
    @if ($company->image_path)
        <img src="{{ asset('storage/' . $company->image_path) }}"
             alt="{{ $company->title }}"
             class="company_show_hero-image">
    @endif

    <h1 class="company_show_title">{{ $company->title }}</h1>

    @if ($company->description)
        <p class="company_show_description">{{ $company->description }}</p>
    @endif

    {{-- 沿革（年表） --}}
    @if ($company->historyEntries->isNotEmpty())
        <section class="company_show_section">
            <h2 class="company_show_section-title">沿革</h2>
            <ol class="company_show_timeline">
                @foreach ($company->historyEntries as $entry)
                    <li class="company_show_timeline-item">
                        <div class="company_show_timeline-year">{{ $entry->year }}</div>
                        <div class="company_show_timeline-marker"></div>
                        <div class="company_show_timeline-event">{{ $entry->event }}</div>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif

    {{-- 実績 --}}
    @if ($company->achievements->isNotEmpty())
        <section class="company_show_section">
            <h2 class="company_show_section-title">実績</h2>
            <div class="company_show_achievements-grid">
                @foreach ($company->achievements as $achievement)
                    <div class="company_show_achievement-card">
                        <p class="company_show_achievement-title">{{ $achievement->title }}</p>
                        @if ($achievement->description)
                            <p class="company_show_achievement-description">{{ $achievement->description }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- 一覧に戻る --}}
    <div class="company_show_back-wrap">
        <a href="{{ route('home') }}" class="company_show_back-link">
            <svg xmlns="http://www.w3.org/2000/svg" class="company_show_back-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            TOPへ戻る
        </a>
    </div>

</div>
@endsection
