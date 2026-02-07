@props([
    'label',
    'value',
    'cardBg' => 'bg-white',
    'ringColor' => 'ring-gray-100',
    'iconBg' => 'bg-gray-100',
    'iconColor' => 'text-gray-700',
])

@php
    $gradients = [
        'blue' => 'from-blue-400 to-blue-600 shadow-blue-500/30',
        'purple' => 'from-purple-400 to-purple-600 shadow-purple-500/30',
        'green' => 'from-green-400 to-green-600 shadow-green-500/30',
        'emerald' => 'from-emerald-400 to-emerald-600 shadow-emerald-500/30',
        'violet' => 'from-violet-400 to-violet-600 shadow-violet-500/30',
        'amber' => 'from-amber-400 to-amber-600 shadow-amber-500/30',
        'cyan' => 'from-cyan-400 to-cyan-600 shadow-cyan-500/30',
        'pink' => 'from-pink-400 to-pink-600 shadow-pink-500/30',
        'indigo' => 'from-indigo-400 to-indigo-600 shadow-indigo-500/30',
    ];
    
    $colorKey = explode('-', $iconColor)[1] ?? 'blue';
    $gradient = $gradients[$colorKey] ?? $gradients['blue'];
@endphp

<div class="relative overflow-hidden rounded-2xl {{ $cardBg }} p-5 shadow-sm ring-1 {{ $ringColor }} transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
    <div class="absolute -right-6 -top-6 h-20 w-20 rounded-full opacity-5 {{ str_replace('text-', 'bg-', $iconColor) }}"></div>
    <div class="flex items-center gap-4">
        @php
            $iconSlot = $icon ?? null;
            $iconContent = $iconSlot && ! $iconSlot->isEmpty() ? $iconSlot : $slot;
            $hasIcon = $iconContent && ! $iconContent->isEmpty();
        @endphp

        @if ($hasIcon)
            <div class="icon-3d grid h-14 w-14 place-items-center rounded-xl bg-gradient-to-br {{ $gradient }} text-white shadow-lg">
                {{ $iconContent }}
            </div>
        @endif
        <div class="min-w-0">
            <div class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $label }}</div>
            <div class="mt-1.5 text-2xl font-bold tracking-tight text-gray-900">{{ $value }}</div>
        </div>
    </div>
</div>
