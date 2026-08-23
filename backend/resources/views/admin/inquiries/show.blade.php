@extends('admin.layouts.app')

@section('title', 'お問い合わせ詳細')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-inquiries.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.inquiries.index') }}" class="common_back-link">
        ← お問い合わせ一覧へ戻る
    </a>
@endsection

@section('content')
<div class="admin_inquiries_show_layout">

    {{-- 問い合わせ情報 --}}
    <div class="admin_card admin_inquiries_show_section">
        <div class="admin_inquiries_show_summary-header">
            <div>
                <p class="admin_inquiries_show_id-label">お問い合わせID</p>
                <p class="admin_inquiries_show_id">#{{ $inquiry->id }}</p>
            </div>
            <span class="common_status-badge {{ $inquiry->status_badge_color }}">
                <span class="common_status-badge__dot"></span>{{ $inquiry->status_label }}
            </span>
        </div>
        <dl class="common_info-list">
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">名前</dt>
                <dd class="common_info-value">{{ $inquiry->name }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">メールアドレス</dt>
                <dd class="common_info-value common_info-value--breakall">{{ $inquiry->email }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">問い合わせ種別</dt>
                <dd class="common_info-value">{{ $inquiry->type_label }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">件名</dt>
                <dd class="common_info-value">{{ $inquiry->subject }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">受付日時</dt>
                <dd class="common_info-value">{{ $inquiry->created_at->format('Y年m月d日 H:i') }}</dd>
            </div>
            <div class="common_info-row common_info-row--compact">
                <dt class="common_info-label">更新日時</dt>
                <dd class="common_info-value">{{ $inquiry->updated_at->format('Y年m月d日 H:i') }}</dd>
            </div>
        </dl>
    </div>

    {{-- 問い合わせ内容 --}}
    <div class="admin_card admin_inquiries_show_section">
        <div class="admin_card-header">
            <h2 class="common_section-title">問い合わせ内容</h2>
        </div>
        <p class="admin_inquiries_show_body">{{ $inquiry->body }}</p>
    </div>

    {{-- ステータス変更 --}}
    <div class="admin_card">
        <div class="admin_card-header">
            <h2 class="common_section-title">ステータス変更</h2>
        </div>
        <form method="POST" action="{{ route('admin.inquiries.updateStatus', $inquiry) }}" class="admin_inquiries_show_status-form">
            @csrf
            @method('PATCH')
            <div>
                <label for="status" class="common_admin-form-label">ステータス</label>
                <select id="status" name="status" class="common_admin-form-input common_admin-form-input--auto">
                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}" @selected($inquiry->status === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="common_button-primary--compact">
                更新する
            </button>
        </form>
    </div>

</div>
@endsection
