@extends('layouts.app')

@section('title', '購入者情報入力')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    {{-- ステップインジケーター --}}
    @include('checkout._steps', ['step' => 1])

    <h1 class="text-2xl font-bold text-gray-900 mb-8">購入者情報の入力</h1>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('checkout.confirm') }}" novalidate>
        @csrf

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-8 py-8 flex flex-col gap-6">

            {{-- 氏名 --}}
            <div>
                <label for="recipient_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                    氏名 <span class="text-red-500">*</span>
                </label>
                <input type="text" id="recipient_name" name="recipient_name"
                       value="{{ old('recipient_name', $user?->name) }}"
                       required autocomplete="name"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('recipient_name') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                       placeholder="山田 太郎">
                @error('recipient_name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- 郵便番号 --}}
            <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1.5">
                    郵便番号 <span class="text-red-500">*</span>
                </label>
                <input type="text" id="postal_code" name="postal_code"
                       value="{{ old('postal_code', $user?->postal_code) }}"
                       required autocomplete="postal-code"
                       class="w-44 px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('postal_code') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                       placeholder="123-4567">
                @error('postal_code')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- 都道府県 --}}
            <div>
                <label for="prefecture" class="block text-sm font-medium text-gray-700 mb-1.5">
                    都道府県 <span class="text-red-500">*</span>
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
                        class="w-44 px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('prefecture') border-red-400 bg-red-50 @else border-gray-300 @enderror">
                    <option value="">選択してください</option>
                    @foreach ($prefectures as $pref)
                        <option value="{{ $pref }}" {{ $selectedPref === $pref ? 'selected' : '' }}>{{ $pref }}</option>
                    @endforeach
                </select>
                @error('prefecture')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- 住所 --}}
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">
                    住所（市区町村以降） <span class="text-red-500">*</span>
                </label>
                <input type="text" id="address" name="address"
                       value="{{ old('address', $user?->address) }}"
                       required autocomplete="street-address"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('address') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                       placeholder="渋谷区〇〇町1-2-3">
                @error('address')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- 電話番号 --}}
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1.5">
                    電話番号 <span class="text-red-500">*</span>
                </label>
                <input type="tel" id="phone_number" name="phone_number"
                       value="{{ old('phone_number', $user?->phone_number) }}"
                       required autocomplete="tel"
                       class="w-56 px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('phone_number') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                       placeholder="090-1234-5678">
                @error('phone_number')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- メールアドレス --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                    メールアドレス <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $user?->email) }}"
                       required autocomplete="email"
                       class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                       placeholder="example@email.com">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="mt-6 flex flex-col sm:flex-row justify-between gap-3">
            <a href="{{ route('cart.index') }}"
               class="text-center px-6 py-3 rounded-xl border border-gray-300 text-sm text-gray-600 hover:bg-gray-100 transition">
                ← カートへ戻る
            </a>
            <button type="submit"
                    class="px-8 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition">
                注文内容の確認へ →
            </button>
        </div>
    </form>

</div>
@endsection
