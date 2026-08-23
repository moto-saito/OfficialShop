@extends('admin.layouts.app')

@section('title', 'お問い合わせ管理')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-inquiries.css') }}">
@endpush

@section('content')

<div class="admin_card">
    <div class="admin_inquiries_index_table-scroll">
        <table class="common_table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th class="admin_inquiries_index_col-type">種別</th>
                    <th>件名</th>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th class="admin_inquiries_index_col-status">ステータス</th>
                    <th class="admin_inquiries_index_col-date">受付日時</th>
                    <th class="admin_inquiries_index_col-date">更新日時</th>
                    <th class="admin_inquiries_index_col-action"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr class="{{ $inquiry->status === 'unread' ? 'is-unread' : '' }}">
                        <td>{{ $inquiry->id }}</td>
                        <td>{{ $inquiry->type_label }}</td>
                        <td class="admin_inquiries_index_subject-cell">{{ $inquiry->subject }}</td>
                        <td>{{ $inquiry->name }}</td>
                        <td class="admin_inquiries_index_email-cell">{{ $inquiry->email }}</td>
                        <td>
                            <span class="common_status-badge {{ $inquiry->status_badge_color }}">
                                <span class="common_status-badge__dot"></span>{{ $inquiry->status_label }}
                            </span>
                        </td>
                        <td>{{ $inquiry->created_at->format('Y/m/d H:i') }}</td>
                        <td>{{ $inquiry->updated_at->format('Y/m/d H:i') }}</td>
                        <td class="admin_inquiries_index_action-cell">
                            <a href="{{ route('admin.inquiries.show', $inquiry) }}"
                               class="common_button-outline--xs">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="admin_inquiries_index_empty">
                            お問い合わせはありません
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($inquiries->hasPages())
        <div class="admin_inquiries_index_pagination-wrap">
            {{ $inquiries->links() }}
        </div>
    @endif
</div>
@endsection
