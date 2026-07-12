@extends('layouts.app')

@section('title', 'ログイン')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/auth.css') }}">
@endpush

@section('content')
<div class="auth_layout">
    <div class="auth_wrap">

        <div class="auth_header">
            <h1 class="auth_title">ログイン</h1>
            <p class="auth_subtext">
                アカウントをお持ちでない方は
                <a href="{{ route('register') }}" class="auth_subtext-link">新規会員登録</a>
            </p>
        </div>

        <div class="common_card common_card--bordered auth_card">

            @if ($errors->any())
                <div class="common_flash-error">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="common_flash-success">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                {{-- メールアドレス --}}
                <div class="auth_field">
                    <label for="email" class="common_form-label">
                        メールアドレス
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="email"
                           class="common_form-input @error('email') is-invalid @enderror"
                           placeholder="example@email.com">
                </div>

                {{-- パスワード --}}
                <div class="auth_field--mb6">
                    <label for="password" class="common_form-label">
                        パスワード
                    </label>
                    <input type="password" id="password" name="password"
                           required autocomplete="current-password"
                           class="common_form-input"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="auth_submit-button">
                    ログイン
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
