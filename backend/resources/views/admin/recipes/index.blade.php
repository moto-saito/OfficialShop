@extends('admin.layouts.app')

@section('title', 'レシピ管理')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-recipes.css') }}">
@endpush

@section('header-action')
    <a href="{{ route('admin.recipes.create') }}"
       class="admin_header-action-button">
        ＋ 新規作成
    </a>
@endsection

@section('content')
<div class="admin_card">
    <table class="common_table">
        <thead>
            <tr>
                <th class="admin_recipes_index_col-id">ID</th>
                <th>タイトル</th>
                <th class="admin_recipes_index_col-status">ステータス</th>
                <th class="admin_recipes_index_col-date">公開日時</th>
                <th class="admin_recipes_index_col-date">作成日時</th>
                <th class="admin_recipes_index_col-action"></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recipes as $recipe)
                <tr>
                    <td class="admin_recipes_index_id-cell">{{ $recipe->id }}</td>

                    <td>
                        <p class="admin_recipes_index_title">{{ $recipe->title }}</p>
                    </td>

                    <td>
                        @if ($recipe->status === 'published' && $recipe->published_at && $recipe->published_at->lte(now()))
                            <span class="common_status-badge common_status-badge--success">
                                <span class="common_status-badge__dot"></span>公開中
                            </span>
                        @elseif ($recipe->status === 'published')
                            <span class="common_status-badge common_status-badge--warning">
                                <span class="common_status-badge__dot"></span>予約済
                            </span>
                        @else
                            <span class="common_status-badge common_status-badge--neutral">
                                <span class="common_status-badge__dot"></span>非公開
                            </span>
                        @endif
                    </td>

                    <td class="admin_recipes_index_date-cell">
                        {{ $recipe->published_at ? $recipe->published_at->format('Y/m/d H:i') : '―' }}
                    </td>

                    <td class="admin_recipes_index_date-cell">
                        {{ $recipe->created_at->format('Y/m/d H:i') }}
                    </td>

                    <td>
                        <div class="admin_recipes_index_actions">
                            {{-- 公開/非公開切り替え --}}
                            <form method="POST" action="{{ route('admin.recipes.toggle', $recipe) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="common_button-toggle--xs {{ $recipe->status === 'published' ? 'is-published' : 'is-draft' }}">
                                    {{ $recipe->status === 'published' ? '非公開にする' : '公開する' }}
                                </button>
                            </form>

                            {{-- 編集 --}}
                            <a href="{{ route('admin.recipes.edit', $recipe) }}"
                               class="common_button-outline--xs">
                                編集
                            </a>

                            {{-- 削除 --}}
                            <form method="POST" action="{{ route('admin.recipes.destroy', $recipe) }}"
                                  onsubmit="return confirm('「{{ $recipe->title }}」を削除しますか？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="common_button-danger--xs">
                                    削除
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="admin_recipes_index_empty">
                        レシピがありません
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($recipes->hasPages())
        <div class="admin_recipes_index_pagination-wrap">
            {{ $recipes->links() }}
        </div>
    @endif
</div>
@endsection
