@php
    $steps = [
        1 => '購入者情報',
        2 => '注文確認',
        3 => '完了',
    ];
@endphp
<div class="flex items-center justify-center gap-0 mb-10">
    @foreach ($steps as $num => $label)
        @php
            $isActive   = $num === $step;
            $isComplete = $num < $step;
        @endphp

        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                    {{ $isActive   ? 'bg-indigo-600 text-white' : '' }}
                    {{ $isComplete ? 'bg-indigo-200 text-indigo-700' : '' }}
                    {{ !$isActive && !$isComplete ? 'bg-gray-200 text-gray-500' : '' }}">
                    {{ $isComplete ? '✓' : $num }}
                </div>
                <span class="mt-1 text-xs {{ $isActive ? 'text-indigo-600 font-semibold' : 'text-gray-400' }}">
                    {{ $label }}
                </span>
            </div>

            @if (!$loop->last)
                <div class="w-16 sm:w-24 h-px bg-gray-300 mb-4 mx-2"></div>
            @endif
        </div>
    @endforeach
</div>
