<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン | {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('css/layout.css') }}">
</head>
<body class="admin_auth_login_body">

    <div class="admin_auth_login_wrap">

        {{-- ロゴ・タイトル --}}
        <div class="admin_auth_login_header">
            <p class="admin_auth_login_eyebrow">{{ config('app.name') }}</p>
            <h1 class="admin_auth_login_title">管理者ログイン</h1>
        </div>

        {{-- カード --}}
        <div class="admin_auth_login_card">

            @if ($errors->any())
                <div class="common_flash-error">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" novalidate>
                @csrf

                {{-- メールアドレス --}}
                <div class="admin_auth_login_field">
                    <label for="email" class="common_form-label">
                        メールアドレス
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        autocomplete="email"
                        class="common_form-input admin_auth_login_input @error('email') is-invalid @enderror"
                        placeholder="admin@example.com"
                    >
                </div>

                {{-- パスワード --}}
                <div class="admin_auth_login_field--last">
                    <label for="password" class="common_form-label">
                        パスワード
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="common_form-input admin_auth_login_input"
                        placeholder="••••••••"
                    >
                </div>

                {{-- ログインボタン --}}
                <button
                    type="submit"
                    class="admin_auth_login_submit"
                >
                    ログイン
                </button>
            </form>
        </div>

        <p class="admin_auth_login_footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>

</body>
</html>
