@extends('layouts.app')

@section('title', $store->title)

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/store-show.css') }}">
@endpush

@section('content')
<div class="store_show_layout">

    {{-- パンくず --}}
    <nav class="common_breadcrumb">
        <span class="common_breadcrumb-current">{{ $store->title }}</span>
    </nav>

    {{-- メイン画像 --}}
    @if ($store->image_path)
        <img src="{{ asset('storage/' . $store->image_path) }}"
             alt="{{ $store->title }}"
             class="store_show_hero-image">
    @endif

    <h1 class="store_show_title">{{ $store->title }}</h1>

    @if ($store->description)
        <p class="store_show_description">{{ $store->description }}</p>
    @endif

    {{-- 基本情報 --}}
    <section class="store_show_section">
        <h2 class="store_show_section-title">基本情報</h2>
        <dl class="store_show_info-list">
            @if ($store->address)
                <div class="store_show_info-row">
                    <dt class="store_show_info-label">住所</dt>
                    <dd class="store_show_info-value">{{ $store->address }}</dd>
                </div>
            @endif
            @if ($store->business_hours)
                <div class="store_show_info-row">
                    <dt class="store_show_info-label">営業時間</dt>
                    <dd class="store_show_info-value">{{ $store->business_hours }}</dd>
                </div>
            @endif
        </dl>
    </section>

    {{-- 交通アクセス --}}
    @if ($store->access)
        <section class="store_show_section">
            <h2 class="store_show_section-title">交通アクセス</h2>
            <p class="store_show_access-text">{{ $store->access }}</p>
        </section>
    @endif

    {{-- MAP --}}
    <section class="store_show_section">
        <h2 class="store_show_section-title">MAP</h2>

        @if ($store->map_image_path)
            <img src="{{ asset('storage/' . $store->map_image_path) }}"
                 alt="交通MAP"
                 class="store_show_map-image">
        @else
            <div class="store_show_map-placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" class="store_show_map-placeholder-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                </svg>
                <p class="store_show_map-placeholder-caption">MAP準備中</p>
            </div>
        @endif
    </section>

    {{-- 一覧に戻る --}}
    <div class="store_show_back-wrap">
        <a href="{{ route('home') }}" class="store_show_back-link">
            <svg xmlns="http://www.w3.org/2000/svg" class="store_show_back-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            TOPへ戻る
        </a>
    </div>

</div>
@endsection
