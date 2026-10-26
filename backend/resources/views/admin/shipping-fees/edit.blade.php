@extends('admin.layouts.app')

@section('title', '送料設定')

@push('head')
<link rel="stylesheet" href="{{ asset('css/pages/admin-shipping-fees.css') }}">
@endpush

@section('content')
@php
    // 入力欄を地方ごとにまとめて表示する（キーは ShippingFee::PREFECTURES の添字範囲）
    $regions = [
        '北海道・東北' => [0, 7],
        '関東'         => [7, 7],
        '中部'         => [14, 9],
        '近畿'         => [23, 7],
        '中国'         => [30, 5],
        '四国'         => [35, 4],
        '九州・沖縄'   => [39, 8],
    ];
    $prefectures = \App\Models\ShippingFee::PREFECTURES;
@endphp

<div class="admin_shipping_fees_page">
    <form method="POST" action="{{ route('admin.shipping-fees.update') }}">
        @csrf
        @method('PUT')

        <div class="admin_form-card">
            <p class="common_admin-form-hint admin_shipping_fees_note">
                配送先の都道府県ごとの送料（税込・円）を設定します。注文確認画面で商品小計に自動で加算されます。
            </p>

            {{-- 一括入力 --}}
            <div class="admin_shipping_fees_bulk">
                <label for="bulk-fee" class="common_admin-form-label">全都道府県に一括入力</label>
                <div class="admin_shipping_fees_bulk-row">
                    <input type="number" id="bulk-fee" min="0" max="99999" step="1"
                           class="common_admin-form-input admin_shipping_fees_input"
                           placeholder="例: 800">
                    <button type="button" id="bulk-apply" class="common_button-outline--compact">
                        反映する
                    </button>
                </div>
                <p class="common_admin-form-hint">入力欄に反映されるだけで、保存は「更新する」で行います</p>
            </div>

            @foreach ($regions as $region => [$offset, $length])
                <fieldset class="admin_shipping_fees_region">
                    <legend class="admin_shipping_fees_region-title">{{ $region }}</legend>
                    <div class="admin_shipping_fees_grid">
                        @foreach (array_slice($prefectures, $offset, $length) as $prefecture)
                            <div>
                                <label for="fee-{{ $loop->parent->index }}-{{ $loop->index }}" class="common_admin-form-label">
                                    {{ $prefecture }}
                                </label>
                                <div class="admin_shipping_fees_input-wrap">
                                    <span class="admin_shipping_fees_prefix">¥</span>
                                    <input type="number"
                                           id="fee-{{ $loop->parent->index }}-{{ $loop->index }}"
                                           name="fees[{{ $prefecture }}]"
                                           value="{{ old('fees.' . $prefecture, $fees[$prefecture]) }}"
                                           min="0" max="99999" step="1" required
                                           class="common_admin-form-input admin_shipping_fees_input js-shipping-fee @error('fees.' . $prefecture) is-invalid @enderror">
                                </div>
                                @error('fees.' . $prefecture)
                                    <p class="common_admin-form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
        </div>

        <div class="admin_form-actions">
            <button type="submit" class="common_button-primary--compact">
                更新する
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('bulk-apply').addEventListener('click', () => {
        const value = document.getElementById('bulk-fee').value;
        if (value === '') return;
        document.querySelectorAll('.js-shipping-fee').forEach((input) => {
            input.value = value;
        });
    });
</script>
@endpush
