@extends('layouts.app')

@section('title', 'マイページ')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/mypage.css') }}">
@endpush

@section('content')
<div class="mypage_layout">

    <div class="common_page-header">
        <h1 class="common_page-header-title">マイページ</h1>
        <a href="{{ route('mypage.edit') }}"
           class="common_button-primary--compact">
            プロフィール編集
        </a>
    </div>

    @if (session('success'))
        <div class="common_flash-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- プロフィール情報 --}}
    <div class="common_card common_card--bordered mypage_section-card">
        <div class="mypage_section-header">
            <h2 class="mypage_section-title">基本情報</h2>
        </div>
        <dl class="common_info-list">
            <div class="common_info-row">
                <dt class="common_info-label common_info-label--strong">氏名</dt>
                <dd class="common_info-value">{{ $user->name }}</dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label common_info-label--strong">メールアドレス</dt>
                <dd class="common_info-value common_info-value--breakall">{{ $user->email }}</dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label common_info-label--strong">郵便番号</dt>
                <dd class="common_info-value">
                    @if ($user->postal_code)
                        <span>〒 {{ $user->postal_code }}</span>
                    @else
                        <span class="common_info-value--muted">未登録</span>
                    @endif
                </dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label common_info-label--strong">住所</dt>
                <dd class="common_info-value">
                    @if ($user->prefecture || $user->address)
                        <span>{{ $user->prefecture }}{{ $user->address }}</span>
                    @else
                        <span class="common_info-value--muted">未登録</span>
                    @endif
                </dd>
            </div>
            <div class="common_info-row">
                <dt class="common_info-label common_info-label--strong">電話番号</dt>
                <dd class="common_info-value">
                    @if ($user->phone_number)
                        <span>{{ $user->phone_number }}</span>
                    @else
                        <span class="common_info-value--muted">未登録</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    {{-- 注文履歴リンク --}}
    <a href="{{ route('mypage.orders.index') }}"
       class="common_card common_card--bordered mypage_index_orders-link">
        <div class="mypage_index_orders-link-left">
            <div class="mypage_index_orders-icon-wrap">
                <svg class="mypage_index_orders-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="mypage_index_orders-title">注文履歴</p>
                <p class="mypage_index_orders-sub">過去のご注文を確認できます</p>
            </div>
        </div>
        <svg class="mypage_index_orders-arrow" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

</div>
@endsection
