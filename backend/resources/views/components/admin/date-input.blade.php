{{--
    管理画面用の日付入力（年・月・日を手入力 + カレンダー）
    使い方: <x-admin.date-input name="date_from" :value="$filters['date_from'] ?? ''" label="開始日" class="..." />
    - 送信値は hidden の name に YYYY-MM-DD で入る（空欄なら空文字）
    - 年欄にフォーカスすると「20」を自動入力。年4桁・月2桁・日2桁を入れると次の欄へ自動で進む
--}}
@props(['name', 'value' => '', 'label' => '日付'])

@php
    // バリデーションエラーで戻ったときは入力値を復元する
    $value = old($name, $value);
    $date = null;
    if (is_string($value) && preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $value, $m) && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
        $date = ['y' => $m[1], 'm' => $m[2], 'd' => $m[3]];
    }
@endphp

@once
    @push('head')
        <link rel="stylesheet" href="{{ asset('css/components/admin-date-input.css') }}">
    @endpush
    @push('scripts')
        <script src="{{ asset('js/admin-date-input.js') }}" defer></script>
    @endpush
@endonce

<div class="admin_date-input-wrap">
<div {{ $attributes->merge(['class' => 'admin_date-input' . ($errors->has($name) ? ' is-invalid' : '')]) }} data-date-input>
    <input type="hidden" name="{{ $name }}" value="{{ $date ? $value : '' }}" data-date-value>

    <div class="admin_date-input_fields" role="group" aria-label="{{ $label }}">
        <input type="text" inputmode="numeric" maxlength="4" placeholder="年"
               class="admin_date-input_part admin_date-input_part--year"
               value="{{ $date['y'] ?? '' }}" aria-label="{{ $label }}（年）" data-part="year" autocomplete="off">
        <span class="admin_date-input_sep">/</span>
        <input type="text" inputmode="numeric" maxlength="2" placeholder="月"
               class="admin_date-input_part"
               value="{{ $date['m'] ?? '' }}" aria-label="{{ $label }}（月）" data-part="month" autocomplete="off">
        <span class="admin_date-input_sep">/</span>
        <input type="text" inputmode="numeric" maxlength="2" placeholder="日"
               class="admin_date-input_part"
               value="{{ $date['d'] ?? '' }}" aria-label="{{ $label }}（日）" data-part="day" autocomplete="off">
    </div>

    <button type="button" class="admin_date-input_calendar-button" aria-label="{{ $label }}をカレンダーから選ぶ" aria-haspopup="dialog" data-calendar-toggle>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="admin_date-input_calendar-icon">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
    </button>

    <div class="admin_date-input_calendar" role="dialog" aria-label="{{ $label }}のカレンダー" hidden data-calendar></div>
</div>
@error($name)
    <p class="admin_date-input_error">{{ $message }}</p>
@enderror
</div>
