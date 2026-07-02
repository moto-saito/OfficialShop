@extends('layouts.app')

@section('title', 'マイページ')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">マイページ</h1>
        <a href="{{ route('mypage.edit') }}"
           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-5 py-2 rounded-lg transition">
            プロフィール編集
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    {{-- プロフィール情報 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">基本情報</h2>
        </div>
        <dl class="divide-y divide-gray-100">
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">氏名</dt>
                <dd class="text-sm text-gray-900 col-span-2">{{ $user->name }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">メールアドレス</dt>
                <dd class="text-sm text-gray-900 col-span-2 break-all">{{ $user->email }}</dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">郵便番号</dt>
                <dd class="text-sm col-span-2">
                    @if ($user->postal_code)
                        <span class="text-gray-900">〒 {{ $user->postal_code }}</span>
                    @else
                        <span class="text-gray-400">未登録</span>
                    @endif
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">住所</dt>
                <dd class="text-sm col-span-2">
                    @if ($user->prefecture || $user->address)
                        <span class="text-gray-900">{{ $user->prefecture }}{{ $user->address }}</span>
                    @else
                        <span class="text-gray-400">未登録</span>
                    @endif
                </dd>
            </div>
            <div class="px-6 py-4 grid grid-cols-3 gap-4">
                <dt class="text-sm font-medium text-gray-500">電話番号</dt>
                <dd class="text-sm col-span-2">
                    @if ($user->phone_number)
                        <span class="text-gray-900">{{ $user->phone_number }}</span>
                    @else
                        <span class="text-gray-400">未登録</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    {{-- 注文履歴リンク --}}
    <a href="{{ route('mypage.orders.index') }}"
       class="flex items-center justify-between bg-white rounded-2xl shadow-sm border border-gray-200 px-6 py-4 hover:bg-gray-50 transition">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center">
                <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-800">注文履歴</p>
                <p class="text-xs text-gray-400">過去のご注文を確認できます</p>
            </div>
        </div>
        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>

</div>
@endsection
