@extends('admin.layouts.app')

@section('title', 'レシピ編集')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-recipes.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.recipes.index') }}"
       class="common_back-link">
        ← 一覧に戻る
    </a>
@endsection

@section('content')
<div class="admin_form-page">
    <form method="POST" action="{{ route('admin.recipes.update', $recipe) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="admin_form-card">
            @include('admin.recipes._form')
        </div>

        <div class="admin_form-actions">
            <a href="{{ route('admin.recipes.index') }}"
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
