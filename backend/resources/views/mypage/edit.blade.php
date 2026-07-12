@extends('layouts.app')

@section('title', 'プロフィール編集')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/mypage.css') }}">
@endpush

@section('content')
<div class="mypage_layout">

    <div class="common_page-header">
        <h1 class="common_page-header-title">プロフィール編集</h1>
        <a href="{{ route('mypage.index') }}"
           class="common_back-link">
            ← マイページへ戻る
        </a>
    </div>

    @if ($errors->any())
        <div class="common_flash-error">
            <ul class="checkout_error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('mypage.update') }}" novalidate>
        @csrf
        @method('PATCH')

        <div class="common_card common_card--bordered mypage_edit_form-card">

            {{-- 基本情報セクション --}}
            <div>
                <h2 class="mypage_edit_section-title">基本情報</h2>
                <div class="mypage_edit_field-group">

                    {{-- 氏名 --}}
                    <div>
                        <label for="name" class="common_form-label">
                            氏名 <span class="common_required-mark">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $user->name) }}"
                               required autocomplete="name"
                               class="common_form-input @error('name') is-invalid @enderror"
                               placeholder="山田 太郎">
                        @error('name')
                            <p class="common_form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- メールアドレス --}}
                    <div>
                        <label for="email" class="common_form-label">
                            メールアドレス <span class="common_required-mark">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               required autocomplete="email"
                               class="common_form-input @error('email') is-invalid @enderror"
                               placeholder="example@email.com">
                        @error('email')
                            <p class="common_form-error">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- 配送先情報セクション（将来の注文・配送機能と連携） --}}
            <div>
                <h2 class="mypage_edit_section-title">配送先情報</h2>
                <div class="mypage_edit_field-group">

                    {{-- 郵便番号 --}}
                    <div>
                        <label for="postal_code" class="common_form-label">郵便番号</label>
                        <input type="text" id="postal_code" name="postal_code"
                               value="{{ old('postal_code', $user->postal_code) }}"
                               autocomplete="postal-code"
                               class="common_form-input mypage_edit_field--w40"
                               placeholder="123-4567">
                        @error('postal_code')
                            <p class="common_form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 都道府県 --}}
                    <div>
                        <label for="prefecture" class="common_form-label">都道府県</label>
                        <select id="prefecture" name="prefecture"
                                class="common_form-input mypage_edit_field--w40">
                            <option value="">選択してください</option>
                            @php
                                $prefectures = [
                                    '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
                                    '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
                                    '新潟県','富山県','石川県','福井県','山梨県','長野県','岐阜県',
                                    '静岡県','愛知県','三重県','滋賀県','京都府','大阪府','兵庫県',
                                    '奈良県','和歌山県','鳥取県','島根県','岡山県','広島県','山口県',
                                    '徳島県','香川県','愛媛県','高知県','福岡県','佐賀県','長崎県',
                                    '熊本県','大分県','宮崎県','鹿児島県','沖縄県',
                                ];
                                $selected = old('prefecture', $user->prefecture);
                            @endphp
                            @foreach ($prefectures as $pref)
                                <option value="{{ $pref }}" {{ $selected === $pref ? 'selected' : '' }}>{{ $pref }}</option>
                            @endforeach
                        </select>
                        @error('prefecture')
                            <p class="common_form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 住所（市区町村以降） --}}
                    <div>
                        <label for="address" class="common_form-label">住所（市区町村以降）</label>
                        <input type="text" id="address" name="address"
                               value="{{ old('address', $user->address) }}"
                               autocomplete="street-address"
                               class="common_form-input"
                               placeholder="渋谷区〇〇町1-2-3">
                        @error('address')
                            <p class="common_form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 電話番号 --}}
                    <div>
                        <label for="phone_number" class="common_form-label">電話番号</label>
                        <input type="tel" id="phone_number" name="phone_number"
                               value="{{ old('phone_number', $user->phone_number) }}"
                               autocomplete="tel"
                               class="common_form-input mypage_edit_field--w56"
                               placeholder="090-1234-5678">
                        @error('phone_number')
                            <p class="common_form-error">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

        </div>

        <div class="mypage_edit_actions">
            <a href="{{ route('mypage.index') }}"
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
