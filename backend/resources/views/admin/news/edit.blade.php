@extends('admin.layouts.app')

@section('title', 'お知らせ編集')

@section('header-action')
    <a href="{{ route('admin.news.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 transition">
        ← 一覧に戻る
    </a>
@endsection

@section('content')
<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col gap-6">
            @include('admin.news._form')
        </div>

        <div class="mt-4 flex justify-end gap-3">
            <a href="{{ route('admin.news.index') }}"
               class="px-5 py-2 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-100 transition">
                キャンセル
            </a>
            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition">
                更新する
            </button>
        </div>
    </form>
</div>
@endsection
