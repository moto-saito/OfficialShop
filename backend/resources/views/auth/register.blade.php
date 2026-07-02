@extends('layouts.app')

@section('title', '新規会員登録')

@section('content')
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">

        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">新規会員登録</h1>
            <p class="mt-2 text-sm text-gray-500">
                すでにアカウントをお持ちの方は
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">こちらからログイン</a>
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-8 py-10">

            @if ($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                {{-- 氏名 --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                        氏名 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name') }}"
                           required autofocus autocomplete="name"
                           class="w-full px-4 py-2.5 border rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           placeholder="山田 太郎">
                </div>

                {{-- メールアドレス --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        メールアドレス <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email') }}"
                           required autocomplete="email"
                           class="w-full px-4 py-2.5 border rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           placeholder="example@email.com">
                </div>

                {{-- パスワード --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        パスワード <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password"
                           required autocomplete="new-password"
                           class="w-full px-4 py-2.5 border rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('password') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                           placeholder="8文字以上">
                    <p class="mt-1 text-xs text-gray-400">半角英数字8文字以上</p>
                </div>

                {{-- パスワード確認 --}}
                <div class="mb-8">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                        パスワード（確認） <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           required autocomplete="new-password"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                           placeholder="もう一度入力してください">
                </div>

                <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold py-3 px-4 rounded-lg transition">
                    会員登録する
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
