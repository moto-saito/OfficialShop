@extends('layouts.app')

@section('title', 'お問い合わせ')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/contact.css') }}">
@endpush

@section('content')
<div class="contact_layout">

    {{-- ステップインジケーター --}}
    @include('contact._steps', ['step' => 1])

    <h1 class="contact_title">お問い合わせ</h1>

    @if ($errors->any())
        <div class="common_flash-error">
            <ul class="contact_error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contact.confirm') }}" novalidate>
        @csrf

        <div class="common_card common_card--bordered contact_form-card">

            {{-- 名前 --}}
            <div>
                <label for="name" class="common_form-label">
                    名前 <span class="common_required-mark">*</span>
                </label>
                <input type="text" id="name" name="name"
                       value="{{ old('name', $user?->name) }}"
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
                       value="{{ old('email', $user?->email) }}"
                       required autocomplete="email"
                       class="common_form-input @error('email') is-invalid @enderror"
                       placeholder="example@email.com">
                @error('email')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 問い合わせ種別 --}}
            <div>
                <label for="type" class="common_form-label">
                    問い合わせ種別 <span class="common_required-mark">*</span>
                </label>
                <select id="type" name="type"
                        required
                        class="common_form-input @error('type') is-invalid @enderror">
                    <option value="">選択してください</option>
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('type')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 件名 --}}
            <div>
                <label for="subject" class="common_form-label">
                    件名 <span class="common_required-mark">*</span>
                </label>
                <input type="text" id="subject" name="subject"
                       value="{{ old('subject') }}"
                       required
                       class="common_form-input @error('subject') is-invalid @enderror"
                       placeholder="お問い合わせの件名をご入力ください">
                @error('subject')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 問い合わせ内容 --}}
            <div>
                <label for="body" class="common_form-label">
                    問い合わせ内容 <span class="common_required-mark">*</span>
                </label>
                <textarea id="body" name="body" rows="8"
                          required
                          class="common_form-input @error('body') is-invalid @enderror"
                          placeholder="お問い合わせ内容をご記入ください">{{ old('body') }}</textarea>
                @error('body')
                    <p class="common_form-error">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="contact_actions">
            <a href="{{ route('home') }}"
               class="common_button-outline--compact">
                ← トップページへ戻る
            </a>
            <button type="submit"
                    class="common_button-primary">
                入力内容の確認へ →
            </button>
        </div>
    </form>

</div>
@endsection
