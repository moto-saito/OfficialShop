<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理者ログイン | {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md px-4">

        {{-- ロゴ・タイトル --}}
        <div class="text-center mb-8">
            <p class="text-xs font-semibold tracking-widest text-gray-400 uppercase mb-2">{{ config('app.name') }}</p>
            <h1 class="text-2xl font-bold text-gray-800">管理者ログイン</h1>
        </div>

        {{-- カード --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-8 py-10">

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}" novalidate>
                @csrf

                {{-- メールアドレス --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
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
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition @error('email') border-red-400 @enderror"
                        placeholder="admin@example.com"
                    >
                </div>

                {{-- パスワード --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        パスワード
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent transition"
                        placeholder="••••••••"
                    >
                </div>

                {{-- ログインボタン --}}
                <button
                    type="submit"
                    class="w-full bg-gray-900 hover:bg-gray-700 text-white text-sm font-semibold py-3 px-4 rounded-lg transition"
                >
                    ログイン
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </p>
    </div>

</body>
</html>
