@extends('layouts.app')

@section('title', 'お問い合わせ完了')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/contact.css') }}">
@endpush

@section('content')
<div class="contact_layout">

    @include('contact._steps', ['step' => 3])

    <div class="contact_complete_body">

        {{-- 完了アイコン --}}
        <div class="common_success-icon-wrap">
            <svg class="common_success-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>

        <h1 class="contact_complete_title">お問い合わせを受け付けました</h1>
        <p class="contact_complete_lead">
            この度はお問い合わせいただき誠にありがとうございます。<br>
            内容を確認のうえ、担当者よりご連絡させていただきます。
        </p>

        <div class="contact_complete_actions">
            <a href="{{ route('home') }}"
               class="common_button-primary">
                トップページへ戻る
            </a>
        </div>
    </div>

</div>
@endsection
