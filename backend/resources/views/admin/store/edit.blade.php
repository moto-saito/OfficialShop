@extends('admin.layouts.app')

@section('title', '店舗・工場情報')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-store.css') }}">
@endpush

@section('content')
<div class="admin_form-page">
    <form method="POST" action="{{ route('admin.store.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="admin_form-card">
            {{-- タイトル --}}
            <div>
                <label class="common_admin-form-label">
                    タイトル <span class="common_required-mark">*</span>
                </label>
                <input type="text" name="title"
                       value="{{ old('title', $store->title) }}"
                       class="common_admin-form-input @error('title') is-invalid @enderror"
                       placeholder="店舗・工場紹介">
                @error('title')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 紹介文 --}}
            <div>
                <label class="common_admin-form-label">紹介文</label>
                <textarea name="description" rows="5"
                          class="common_admin-form-input @error('description') is-invalid @enderror"
                          placeholder="店舗・工場の紹介文">{{ old('description', $store->description) }}</textarea>
                @error('description')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- メイン画像 --}}
            <div>
                <label class="common_admin-form-label">メイン画像</label>

                @if ($store->image_path)
                    <div class="admin_store_form_image-preview-wrap">
                        <img src="{{ asset('storage/' . $store->image_path) }}"
                             alt="現在の画像"
                             class="admin_store_form_image-preview">
                        <p class="common_admin-form-hint">新しい画像をアップロードすると置き換わります</p>
                    </div>
                @endif

                <input type="file" name="image" accept="image/*"
                       class="admin_store_form_file-input">
                <p class="common_admin-form-hint">JPEG・PNG・GIF・WebP（最大 2MB）</p>
                @error('image')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 住所 --}}
            <div>
                <label class="common_admin-form-label">住所</label>
                <input type="text" name="address"
                       value="{{ old('address', $store->address) }}"
                       class="common_admin-form-input @error('address') is-invalid @enderror"
                       placeholder="例: 〇〇県〇〇市〇〇1-2-3">
                @error('address')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 営業時間 --}}
            <div>
                <label class="common_admin-form-label">営業時間</label>
                <input type="text" name="business_hours"
                       value="{{ old('business_hours', $store->business_hours) }}"
                       class="common_admin-form-input @error('business_hours') is-invalid @enderror"
                       placeholder="例: 10:00〜18:00（土日祝休み）">
                @error('business_hours')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 交通アクセス --}}
            <div>
                <label class="common_admin-form-label">交通アクセス</label>
                <textarea name="access" rows="3"
                          class="common_admin-form-input @error('access') is-invalid @enderror"
                          placeholder="例: 〇〇駅から徒歩10分">{{ old('access', $store->access) }}</textarea>
                @error('access')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- MAP画像 --}}
            <div>
                <label class="common_admin-form-label">交通MAP画像</label>

                @if ($store->map_image_path)
                    <div class="admin_store_form_image-preview-wrap">
                        <img src="{{ asset('storage/' . $store->map_image_path) }}"
                             alt="現在のMAP画像"
                             class="admin_store_form_image-preview">
                        <p class="common_admin-form-hint">新しい画像をアップロードすると置き換わります</p>
                    </div>
                @else
                    <p class="common_admin-form-hint">未設定の場合はプレースホルダーが表示されます</p>
                @endif

                <input type="file" name="map_image" accept="image/*"
                       class="admin_store_form_file-input">
                <p class="common_admin-form-hint">JPEG・PNG・GIF・WebP（最大 2MB）</p>
                @error('map_image')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="admin_form-actions">
            <a href="{{ route('store.show') }}" target="_blank" class="common_button-outline--compact">
                公開ページを見る
            </a>
            <button type="submit" class="common_button-primary--compact">
                更新する
            </button>
        </div>
    </form>
</div>
@endsection
