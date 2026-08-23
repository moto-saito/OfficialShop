@extends('layouts.app')

@section('title', 'お問い合わせ内容の確認')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/contact.css') }}">
@endpush

@section('content')
<div class="contact_layout">

    @include('contact._steps', ['step' => 2])

    <h1 class="contact_title">入力内容の確認</h1>

    <div class="common_card common_card--bordered contact_confirm_section-card">
        <dl class="common_info-list">
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">名前</dt>
                <dd class="common_info-value">{{ $validated['name'] }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">メールアドレス</dt>
                <dd class="common_info-value common_info-value--breakall">{{ $validated['email'] }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">問い合わせ種別</dt>
                <dd class="common_info-value">{{ $typeLabel }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">件名</dt>
                <dd class="common_info-value">{{ $validated['subject'] }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">問い合わせ内容</dt>
                <dd class="common_info-value contact_confirm_body">{{ $validated['body'] }}</dd>
            </div>
        </dl>
    </div>

    {{-- 送信フォーム --}}
    <form method="POST" action="{{ route('contact.store') }}">
        @csrf
        <div class="contact_actions">
            <a href="{{ route('contact.index') }}"
               class="common_button-outline--compact">
                ← 入力へ戻る
            </a>
            <button type="submit"
                    class="common_button-primary">
                送信する
            </button>
        </div>
    </form>

</div>
@endsection
