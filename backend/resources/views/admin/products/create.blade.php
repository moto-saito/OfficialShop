@extends('admin.layouts.app')

@section('title', '商品新規登録')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-products.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.products.index') }}"
       class="common_back-link">
        ← 一覧に戻る
    </a>
@endsection

@section('content')
<div class="admin_form-page">
    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="admin_form-card">
            @include('admin.products._form')
        </div>

        <div class="admin_form-actions">
            <a href="{{ route('admin.products.index') }}"
               class="common_button-outline--compact">
                キャンセル
            </a>
            <button type="submit"
                    class="common_button-primary--compact">
                登録する
            </button>
        </div>
    </form>
</div>
@endsection
