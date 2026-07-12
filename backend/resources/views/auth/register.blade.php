@extends('layouts.app')

@section('title', '新規会員登録')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
@endpush

@section('content')
<div class="auth_layout">
    <div class="auth_wrap auth_wrap--wide">

        <div class="auth_header">
            <h1 class="auth_title">新規会員登録</h1>
            <p class="auth_subtext">
                すでにアカウントをお持ちの方は
                <a href="{{ route('login') }}" class="auth_subtext-link">こちらからログイン</a>
            </p>
        </div>

        <div class="common_card common_card--bordered auth_card">

            @if ($errors->any())
                <div class="common_flash-error">
                    <ul class="checkout_error-list">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                {{-- 氏名 --}}
                <div class="auth_field">
                    <label for="name" class="common_form-label">
                        氏名 <span class="common_required-mark">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name') }}"
                           required autofocus autocomplete="name"
                           class="common_form-input @error('name') is-invalid @enderror"
                           placeholder="山田 太郎">
                </div>

                {{-- メールアドレス --}}
                <div class="auth_field">
                    <label for="email" class="common_form-label">
                        メールアドレス <span class="common_required-mark">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           required autocomplete="email"
                           class="common_form-input @error('email') is-invalid @enderror"
                           placeholder="example@email.com">
                </div>

                {{-- パスワード --}}
                <div class="auth_field">
                    <label for="password" class="common_form-label">
                        パスワード <span class="common_required-mark">*</span>
                    </label>
                    <input type="password" id="password" name="password"
                           required autocomplete="new-password"
                           class="common_form-input @error('password') is-invalid @enderror"
                           placeholder="8文字以上">
                    <p class="common_form-hint">半角英数字8文字以上</p>
                </div>

                {{-- パスワード確認 --}}
                <div class="auth_field--mb8">
                    <label for="password_confirmation" class="common_form-label">
                        パスワード（確認） <span class="common_required-mark">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           required autocomplete="new-password"
                           class="common_form-input"
                           placeholder="もう一度入力してください">
                </div>

                <button type="submit"
                        class="auth_submit-button">
                    会員登録する
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
