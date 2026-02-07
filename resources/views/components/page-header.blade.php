@props([
    'title',
    'subtitle' => null,
    'accent' => 'brand',
])

@php
    $accents = [
        'brand' => ['#2563EB', '#4F46E5', '#6366F1'],
        'dashboard' => ['#2563EB', '#4F46E5', '#6366F1'],
        'students' => ['#0284C7', '#2563EB', '#4F46E5'],
        'teachers' => ['#F59E0B', '#EA580C', '#F97316'],
        'classes' => ['#06B6D4', '#0284C7', '#2563EB'],
        'subjects' => ['#7C3AED', '#4F46E5', '#2563EB'],
        'attendance' => ['#38BDF8', '#0EA5E9', '#2563EB'],
        'results' => ['#6366F1', '#4F46E5', '#2563EB'],
        'finance' => ['#16A34A', '#0EA5E9', '#2563EB'],
        'billing' => ['#16A34A', '#0EA5E9', '#2563EB'],
        'accounts' => ['#4F46E5', '#2563EB', '#0EA5E9'],
        'institute' => ['#0EA5E9', '#2563EB', '#4F46E5'],
        'settings' => ['#64748B', '#475569', '#334155'],
        'more' => ['#64748B', '#475569', '#334155'],
    ];

    [$accentFrom, $accentMid, $accentTo] = $accents[$accent] ?? $accents['brand'];
@endphp

<section {{ $attributes->class('relative overflow-hidden rounded-3xl bg-white shadow-soft ring-1 ring-gray-100') }}>
    <div
        class="absolute inset-x-0 top-0 h-1"
        style="background: linear-gradient(90deg, {{ $accentFrom }}, {{ $accentMid }}, {{ $accentTo }});"
    ></div>
    <div class="px-6 py-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            @php
                $leadingSlot = $leading ?? null;
                $leadingContent = $leadingSlot && ! $leadingSlot->isEmpty() ? $leadingSlot : null;
            @endphp

            <div class="flex min-w-0 items-start gap-4">
                @if ($leadingContent)
                    <div class="shrink-0">
                        {{ $leadingContent }}
                    </div>
                @endif
                <div class="min-w-0">
                    <h1 class="truncate text-2xl font-bold tracking-tight text-gray-900">{{ $title }}</h1>
                    @if ($subtitle)
                        <p class="mt-1 text-sm text-gray-600">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            @isset($actions)
                <div class="flex flex-wrap items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    </div>
</section>
