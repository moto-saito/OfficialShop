@php
    $steps = [
        1 => '入力',
        2 => '確認',
        3 => '完了',
    ];
@endphp
<div class="common_steps_wrap">
    @foreach ($steps as $num => $label)
        @php
            $isActive   = $num === $step;
            $isComplete = $num < $step;
        @endphp

        <div class="common_steps_step">
            <div class="common_steps_node">
                <div class="common_steps_circle
                    {{ $isActive ? 'is-active' : '' }}
                    {{ $isComplete ? 'is-complete' : '' }}">
                    {{ $isComplete ? '✓' : $num }}
                </div>
                <span class="common_steps_label {{ $isActive ? 'is-active' : '' }}">
                    {{ $label }}
                </span>
            </div>

            @if (!$loop->last)
                <div class="common_steps_connector"></div>
            @endif
        </div>
    @endforeach
</div>
