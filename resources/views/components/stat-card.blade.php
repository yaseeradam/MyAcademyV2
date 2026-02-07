@props([
    'label',
    'value',
    'cardBg' => 'bg-white',
    'ringColor' => 'ring-gray-100',
    'iconBg' => 'bg-gray-100',
    'iconColor' => 'text-gray-700',
])

<div class="rounded-2xl {{ $cardBg }} p-4 shadow-soft ring-1 {{ $ringColor }}">
    <div class="flex items-center gap-4">
        @php
            $iconSlot = $icon ?? null;
            $iconContent = $iconSlot && ! $iconSlot->isEmpty() ? $iconSlot : $slot;
            $hasIcon = $iconContent && ! $iconContent->isEmpty();
        @endphp

        @if ($hasIcon)
            <div class="grid h-12 w-12 place-items-center rounded-full {{ $iconBg }} {{ $iconColor }}">
                {{ $iconContent }}
            </div>
        @endif
        <div class="min-w-0">
            <div class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $label }}</div>
            <div class="mt-1 text-2xl font-semibold tracking-tight text-gray-900">{{ $value }}</div>
        </div>
    </div>
</div>
