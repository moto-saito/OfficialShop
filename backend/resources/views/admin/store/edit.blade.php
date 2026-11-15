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
                        <img src="{{ Storage::disk('public')->url($store->image_path) }}"
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
                        <img src="{{ Storage::disk('public')->url($store->map_image_path) }}"
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

        {{-- 送り状のご依頼主（ヤマト運輸 B2クラウド用 CSV に使う） --}}
        <div class="admin_form-card admin_store_form_sender-card">
            <div>
                <h2 class="admin_store_form_section-title">送り状のご依頼主（ヤマト運輸 B2クラウド）</h2>
                <p class="common_admin-form-hint">
                    注文詳細の「B2クラウド用CSV」で、送り状のご依頼主（発送元）として使います。
                    空欄の項目は .env の設定（YAMATO_B2_SENDER_*）を使います。
                </p>
            </div>

            <div>
                <label class="common_admin-form-label" for="sender_name">ご依頼主名</label>
                <input type="text" name="sender_name" id="sender_name"
                       value="{{ old('sender_name', $store->sender_name) }}"
                       class="common_admin-form-input @error('sender_name') is-invalid @enderror"
                       placeholder="山一醤油製造所">
                <p class="common_admin-form-hint">全角16文字以内。送り状の「ご依頼主」に印字されます。</p>
                @error('sender_name')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="common_admin-form-label" for="sender_phone">電話番号</label>
                <input type="text" name="sender_phone" id="sender_phone"
                       value="{{ old('sender_phone', $store->sender_phone) }}"
                       class="common_admin-form-input @error('sender_phone') is-invalid @enderror"
                       placeholder="0120-000-000">
                <p class="common_admin-form-hint">ハイフンありで15文字以内</p>
                @error('sender_phone')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="common_admin-form-label" for="sender_postal">郵便番号</label>
                <input type="text" name="sender_postal" id="sender_postal"
                       value="{{ old('sender_postal', $store->sender_postal) }}"
                       class="common_admin-form-input @error('sender_postal') is-invalid @enderror"
                       placeholder="123-4567">
                <p class="common_admin-form-hint">7桁（ハイフンはあってもなくても可）</p>
                @error('sender_postal')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="common_admin-form-label" for="sender_address">住所</label>
                <input type="text" name="sender_address" id="sender_address"
                       value="{{ old('sender_address', $store->sender_address) }}"
                       class="common_admin-form-input @error('sender_address') is-invalid @enderror"
                       placeholder="東京都千代田区千代田1-1">
                <p class="common_admin-form-hint">都道府県から町・番地まで。全角32文字以内</p>
                @error('sender_address')
                    <p class="common_admin-form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="common_admin-form-label" for="sender_building">建物名</label>
                <input type="text" name="sender_building" id="sender_building"
                       value="{{ old('sender_building', $store->sender_building) }}"
                       class="common_admin-form-input @error('sender_building') is-invalid @enderror"
                       placeholder="〇〇ビル 1F">
                <p class="common_admin-form-hint">全角16文字以内（無ければ空欄）</p>
                @error('sender_building')
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
