@extends('admin.layouts.app')

@section('title', '企業歴史・実績')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-company.css') }}">
@endpush

@section('content')
@php
    $historyRows = old('history', $company->historyEntries->map(fn ($e) => ['year' => $e->year, 'event' => $e->event])->toArray());
    $achievementRows = old('achievements', $company->achievements->map(fn ($a) => ['title' => $a->title, 'description' => $a->description])->toArray());
@endphp

<div class="admin_form-page">
    <form method="POST" action="{{ route('admin.company.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="admin_form-card">
            {{-- タイトル --}}
            <div>
                <label class="common_admin-form-label">
                    タイトル <span class="common_required-mark">*</span>
                </label>
                <input type="text" name="title"
                       value="{{ old('title', $company->title) }}"
                       class="common_admin-form-input @error('title') is-invalid @enderror"
                       placeholder="企業歴史・実績">
                @error('title')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 紹介文 --}}
            <div>
                <label class="common_admin-form-label">紹介文</label>
                <textarea name="description" rows="5"
                          class="common_admin-form-input @error('description') is-invalid @enderror"
                          placeholder="企業の歩みについての紹介文">{{ old('description', $company->description) }}</textarea>
                @error('description')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- メイン画像 --}}
            <div>
                <label class="common_admin-form-label">メイン画像</label>

                @if ($company->image_path)
                    <div class="admin_company_form_image-preview-wrap">
                        <img src="{{ asset('storage/' . $company->image_path) }}"
                             alt="現在の画像"
                             class="admin_company_form_image-preview">
                        <p class="common_admin-form-hint">新しい画像をアップロードすると置き換わります</p>
                    </div>
                @endif

                <input type="file" name="image" accept="image/*"
                       class="admin_company_form_file-input">
                <p class="common_admin-form-hint">JPEG・PNG・GIF・WebP（最大 2MB）</p>
                @error('image')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 沿革 --}}
            <div>
                <label class="admin_company_form_section-label">沿革（年表）</label>

                <div id="history-list" class="admin_company_form_history-list">
                    @forelse ($historyRows as $i => $entry)
                        <div class="admin_company_form_history-row">
                            <input type="text" name="history[{{ $i }}][year]"
                                   value="{{ $entry['year'] ?? '' }}"
                                   placeholder="年（例: 1950年）"
                                   class="common_admin-form-input admin_company_form_history-year">
                            <textarea name="history[{{ $i }}][event]" rows="2"
                                      placeholder="出来事（例: 醤油醸造業として創業）"
                                      class="common_admin-form-input admin_company_form_history-event">{{ $entry['event'] ?? '' }}</textarea>
                            <button type="button" class="admin_company_form_row-remove">削除</button>
                        </div>
                    @empty
                        <div class="admin_company_form_history-row">
                            <input type="text" name="history[0][year]"
                                   placeholder="年（例: 1950年）"
                                   class="common_admin-form-input admin_company_form_history-year">
                            <textarea name="history[0][event]" rows="2"
                                      placeholder="出来事（例: 醤油醸造業として創業）"
                                      class="common_admin-form-input admin_company_form_history-event"></textarea>
                            <button type="button" class="admin_company_form_row-remove">削除</button>
                        </div>
                    @endforelse
                </div>

                <button type="button" id="add-history" class="admin_company_form_row-add">
                    ＋ 年表を追加
                </button>
            </div>

            {{-- 実績 --}}
            <div>
                <label class="admin_company_form_section-label">実績</label>

                <div id="achievements-list" class="admin_company_form_achievements-list">
                    @forelse ($achievementRows as $i => $achievement)
                        <div class="admin_company_form_achievement-row">
                            <input type="text" name="achievements[{{ $i }}][title]"
                                   value="{{ $achievement['title'] ?? '' }}"
                                   placeholder="タイトル（例: 〇〇コンテスト金賞）"
                                   class="common_admin-form-input admin_company_form_achievement-title">
                            <textarea name="achievements[{{ $i }}][description]" rows="2"
                                      placeholder="説明（任意）"
                                      class="common_admin-form-input admin_company_form_achievement-description">{{ $achievement['description'] ?? '' }}</textarea>
                            <button type="button" class="admin_company_form_row-remove">削除</button>
                        </div>
                    @empty
                        <div class="admin_company_form_achievement-row">
                            <input type="text" name="achievements[0][title]"
                                   placeholder="タイトル（例: 〇〇コンテスト金賞）"
                                   class="common_admin-form-input admin_company_form_achievement-title">
                            <textarea name="achievements[0][description]" rows="2"
                                      placeholder="説明（任意）"
                                      class="common_admin-form-input admin_company_form_achievement-description"></textarea>
                            <button type="button" class="admin_company_form_row-remove">削除</button>
                        </div>
                    @endforelse
                </div>

                <button type="button" id="add-achievement" class="admin_company_form_row-add">
                    ＋ 実績を追加
                </button>
            </div>
        </div>

        <div class="admin_form-actions">
            <a href="{{ route('company.show') }}" target="_blank" class="common_button-outline--compact">
                公開ページを見る
            </a>
            <button type="submit" class="common_button-primary--compact">
                更新する
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
(function () {
    let historyIndex     = {{ empty($historyRows) ? 1 : max(array_keys($historyRows)) + 1 }};
    let achievementIndex = {{ empty($achievementRows) ? 1 : max(array_keys($achievementRows)) + 1 }};

    const historyList     = document.getElementById('history-list');
    const achievementList = document.getElementById('achievements-list');

    document.getElementById('add-history').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'admin_company_form_history-row';
        row.innerHTML = `
            <input type="text" name="history[${historyIndex}][year]" placeholder="年（例: 1950年）" class="common_admin-form-input admin_company_form_history-year">
            <textarea name="history[${historyIndex}][event]" rows="2" placeholder="出来事（例: 醤油醸造業として創業）" class="common_admin-form-input admin_company_form_history-event"></textarea>
            <button type="button" class="admin_company_form_row-remove">削除</button>
        `;
        historyList.appendChild(row);
        historyIndex++;
    });

    historyList.addEventListener('click', function (e) {
        if (e.target.classList.contains('admin_company_form_row-remove')) {
            e.target.closest('.admin_company_form_history-row').remove();
        }
    });

    document.getElementById('add-achievement').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'admin_company_form_achievement-row';
        row.innerHTML = `
            <input type="text" name="achievements[${achievementIndex}][title]" placeholder="タイトル（例: 〇〇コンテスト金賞）" class="common_admin-form-input admin_company_form_achievement-title">
            <textarea name="achievements[${achievementIndex}][description]" rows="2" placeholder="説明（任意）" class="common_admin-form-input admin_company_form_achievement-description"></textarea>
            <button type="button" class="admin_company_form_row-remove">削除</button>
        `;
        achievementList.appendChild(row);
        achievementIndex++;
    });

    achievementList.addEventListener('click', function (e) {
        if (e.target.classList.contains('admin_company_form_row-remove')) {
            e.target.closest('.admin_company_form_achievement-row').remove();
        }
    });
})();
</script>
@endpush
@endsection
