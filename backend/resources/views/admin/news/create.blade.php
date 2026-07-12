@extends('admin.layouts.app')

@section('title', 'お知らせ新規作成')

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
    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data">
        @csrf

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
                作成する
            </button>
        </div>
    </form>
</div>
@endsection
