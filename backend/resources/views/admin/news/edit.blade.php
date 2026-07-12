@extends('admin.layouts.app')

@section('title', 'お知らせ編集')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-news.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.news.index') }}"
       class="common_back-link">
        ← 一覧に戻る
    </a>
@endsection

@section('content')
<div class="admin_form-page">
    <form method="POST" action="{{ route('admin.news.update', $news) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="admin_form-card">
            @include('admin.news._form')
        </div>

        <div class="admin_form-actions">
            <a href="{{ route('admin.news.index') }}"
               class="common_button-outline--compact">
                キャンセル
            </a>
            <button type="submit"
                    class="common_button-primary--compact">
                更新する
            </button>
        </div>
    </form>
</div>
@endsection
