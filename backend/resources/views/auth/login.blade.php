@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">ログイン</h1>
            <p class="mt-2 text-sm text-gray-500">
                アカウントをお持ちでない方は
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">新規会員登録</a>
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-8 py-10">

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                {{-- メールアドレス --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        メールアドレス
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           required autofocus autocomplete="email"
                           class="w-full px-4 py-2.5 border rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           placeholder="example@email.com">
                </div>

                {{-- パスワード --}}
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        パスワード
                    </label>
                    <input type="password" id="password" name="password"
                           required autocomplete="current-password"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                           placeholder="••••••••">
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-3 px-4 rounded-lg transition">
                    ログイン
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
