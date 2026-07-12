@php
    $steps = [
        1 => '購入者情報',
        2 => '注文確認',
        3 => '完了',
    ];
@endphp
<div class="checkout_steps_wrap">
    @foreach ($steps as $num => $label)
        @php
            $isActive   = $num === $step;
            $isComplete = $num < $step;
        @endphp

        <div class="checkout_steps_step">
            <div class="checkout_steps_node">
                <div class="checkout_steps_circle
                    {{ $isActive ? 'is-active' : '' }}
                    {{ $isComplete ? 'is-complete' : '' }}">
                    {{ $isComplete ? '✓' : $num }}
                </div>
                <span class="checkout_steps_label {{ $isActive ? 'is-active' : '' }}">
                    {{ $label }}
                </span>
            </div>

            @if (!$loop->last)
                <div class="checkout_steps_connector"></div>
            @endif
        </div>
    @endforeach
</div>
