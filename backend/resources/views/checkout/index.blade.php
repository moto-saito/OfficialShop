@extends('layouts.app')

@section('title', '購入者情報入力')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/checkout.css') }}">
@endpush

@section('content')
<div class="checkout_layout">

    {{-- ステップインジケーター --}}
    @include('checkout._steps', ['step' => 1])

    <h1 class="checkout_title">購入者情報の入力</h1>

    @if ($errors->any())
        <div class="common_flash-error">
            <ul class="checkout_error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.confirm') }}" novalidate>
        @csrf

        <div class="common_card common_card--bordered checkout_index_form-card">

            {{-- 氏名 --}}
            <div>
                <label for="recipient_name" class="common_form-label">
                    氏名 <span class="common_required-mark">*</span>
                </label>
                <input type="text" id="recipient_name" name="recipient_name"
                       value="{{ old('recipient_name', $user?->name) }}"
                       required autocomplete="name"
                       class="common_form-input @error('recipient_name') is-invalid @enderror"
                       placeholder="山田 太郎">
                @error('recipient_name')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 郵便番号 --}}
            <div>
                <label for="postal_code" class="common_form-label">
                    郵便番号 <span class="common_required-mark">*</span>
                </label>
                <input type="text" id="postal_code" name="postal_code"
                       value="{{ old('postal_code', $user?->postal_code) }}"
                       required autocomplete="postal-code"
                       class="common_form-input checkout_index_field--w44 @error('postal_code') is-invalid @enderror"
                       placeholder="123-4567">
                @error('postal_code')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 都道府県 --}}
            <div>
                <label for="prefecture" class="common_form-label">
                    都道府県 <span class="common_required-mark">*</span>
                </label>
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
                    $selectedPref = old('prefecture', $user?->prefecture);
                @endphp
                <select id="prefecture" name="prefecture"
                        class="common_form-input checkout_index_field--w44 @error('prefecture') is-invalid @enderror">
                    <option value="">選択してください</option>
                    @foreach ($prefectures as $pref)
                        <option value="{{ $pref }}" {{ $selectedPref === $pref ? 'selected' : '' }}>{{ $pref }}</option>
                    @endforeach
                </select>
                @error('prefecture')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 住所 --}}
            <div>
                <label for="address" class="common_form-label">
                    住所（市区町村以降） <span class="common_required-mark">*</span>
                </label>
                <input type="text" id="address" name="address"
                       value="{{ old('address', $user?->address) }}"
                       required autocomplete="street-address"
                       class="common_form-input @error('address') is-invalid @enderror"
                       placeholder="渋谷区〇〇町1-2-3">
                @error('address')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 電話番号 --}}
            <div>
                <label for="phone_number" class="common_form-label">
                    電話番号 <span class="common_required-mark">*</span>
                </label>
                <input type="tel" id="phone_number" name="phone_number"
                       value="{{ old('phone_number', $user?->phone_number) }}"
                       required autocomplete="tel"
                       class="common_form-input checkout_index_field--w56 @error('phone_number') is-invalid @enderror"
                       placeholder="090-1234-5678">
                @error('phone_number')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- メールアドレス --}}
            <div>
                <label for="email" class="common_form-label">
                    メールアドレス <span class="common_required-mark">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $user?->email) }}"
                       required autocomplete="email"
                       class="common_form-input @error('email') is-invalid @enderror"
                       placeholder="example@email.com">
                @error('email')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="checkout_actions">
            <a href="{{ route('cart.index') }}"
               class="checkout_secondary-button">
                ← カートへ戻る
            </a>
            <button type="submit"
                    class="checkout_primary-button">
                注文内容の確認へ →
            </button>
        </div>
    </form>

</div>
@endsection
