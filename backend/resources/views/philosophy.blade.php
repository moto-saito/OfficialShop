@extends('layouts.app')

@section('title', '企業理念 | ' . config('app.name'))

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/philosophy.css') }}">
@endpush

@section('content')
<section class="philosophy_section">
    <div class="philosophy_container philosophy_inner">

        {{-- 企業理念テキスト --}}
        <div class="philosophy_text">
            <p class="philosophy_eyebrow">Our Philosophy</p>
            <h1 class="philosophy_title">企業理念</h1>
            <p class="philosophy_desc">
                私たちは「お客様の生活をより豊かに」という信念のもと、
                厳選された素材と丁寧な製造で、長く愛される商品をお届けします。
            </p>
        </div>

        {{-- 会社写真エリア（プレースホルダー） --}}
        <div class="philosophy_photo">
            <div class="philosophy_photo-frame">
                {{-- 実際の写真に差し替え可能 --}}
                <div class="philosophy_photo-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" class="philosophy_photo-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="philosophy_photo-caption">会社写真</p>
                </div>
            </div>
        </div>
    </div>

    {{-- TOPへ戻る --}}
    <div class="philosophy_container philosophy_back-wrap">
        <a href="{{ route('home') }}" class="philosophy_back-link">
            <svg xmlns="http://www.w3.org/2000/svg" class="philosophy_back-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            TOPへ戻る
        </a>
    </div>
</section>
@endsection
