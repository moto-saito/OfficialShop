@extends('layouts.app')

@section('title', 'プロフィール編集')

@section('content')
<div class="max-w-2xl mx-auto px-4 py-12">

    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-gray-900">プロフィール編集</h1>
        <a href="{{ route('mypage.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 transition">
            ← マイページへ戻る
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('mypage.update') }}" novalidate>
        @csrf
        @method('PATCH')

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 px-8 py-8 flex flex-col gap-6">

            {{-- 基本情報セクション --}}
            <div>
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">基本情報</h2>
                <div class="flex flex-col gap-5">

                    {{-- 氏名 --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            氏名 <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $user->name) }}"
                               required autocomplete="name"
                               class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('name') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                               placeholder="山田 太郎">
                        @error('name')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- メールアドレス --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                            メールアドレス <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                               value="{{ old('email', $user->email) }}"
                               required autocomplete="email"
                               class="w-full px-4 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-400 bg-red-50 @else border-gray-300 @enderror"
                               placeholder="example@email.com">
                        @error('email')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- 配送先情報セクション（将来の注文・配送機能と連携） --}}
            <div>
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4 pb-2 border-b border-gray-100">配送先情報</h2>
                <div class="flex flex-col gap-5">

                    {{-- 郵便番号 --}}
                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-1.5">郵便番号</label>
                        <input type="text" id="postal_code" name="postal_code"
                               value="{{ old('postal_code', $user->postal_code) }}"
                               autocomplete="postal-code"
                               class="w-40 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="123-4567">
                        @error('postal_code')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 都道府県 --}}
                    <div>
                        <label for="prefecture" class="block text-sm font-medium text-gray-700 mb-1.5">都道府県</label>
                        <select id="prefecture" name="prefecture"
                                class="w-40 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
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
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 住所（市区町村以降） --}}
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">住所（市区町村以降）</label>
                        <input type="text" id="address" name="address"
                               value="{{ old('address', $user->address) }}"
                               autocomplete="street-address"
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="渋谷区〇〇町1-2-3">
                        @error('address')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 電話番号 --}}
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-1.5">電話番号</label>
                        <input type="tel" id="phone_number" name="phone_number"
                               value="{{ old('phone_number', $user->phone_number) }}"
                               autocomplete="tel"
                               class="w-56 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                               placeholder="090-1234-5678">
                        @error('phone_number')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

        </div>

        <div class="mt-4 flex justify-end gap-3">
            <a href="{{ route('mypage.index') }}"
               class="px-5 py-2 rounded-lg border border-gray-300 text-sm text-gray-600 hover:bg-gray-100 transition">
                キャンセル
            </a>
            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium transition">
                更新する
            </button>
        </div>
    </form>

</div>
@endsection
