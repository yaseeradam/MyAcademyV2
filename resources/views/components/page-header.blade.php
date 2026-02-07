@props([
    'title',
    'subtitle' => null,
    'accent' => 'brand',
])

@php
    $accents = [
        'brand' => ['from' => '#2563EB', 'via' => '#4F46E5', 'to' => '#6366F1', 'bg' => 'from-blue-50/80 via-indigo-50/60 to-purple-50/40'],
        'dashboard' => ['from' => '#2563EB', 'via' => '#4F46E5', 'to' => '#6366F1', 'bg' => 'from-blue-50/80 via-indigo-50/60 to-purple-50/40'],
        'students' => ['from' => '#0284C7', 'via' => '#2563EB', 'to' => '#4F46E5', 'bg' => 'from-cyan-50/80 via-blue-50/60 to-indigo-50/40'],
        'teachers' => ['from' => '#F59E0B', 'via' => '#EA580C', 'to' => '#F97316', 'bg' => 'from-amber-50/80 via-orange-50/60 to-red-50/40'],
        'classes' => ['from' => '#06B6D4', 'via' => '#0284C7', 'to' => '#2563EB', 'bg' => 'from-cyan-50/80 via-sky-50/60 to-blue-50/40'],
        'subjects' => ['from' => '#7C3AED', 'via' => '#4F46E5', 'to' => '#2563EB', 'bg' => 'from-violet-50/80 via-indigo-50/60 to-blue-50/40'],
        'attendance' => ['from' => '#38BDF8', 'via' => '#0EA5E9', 'to' => '#2563EB', 'bg' => 'from-sky-50/80 via-blue-50/60 to-indigo-50/40'],
        'results' => ['from' => '#6366F1', 'via' => '#4F46E5', 'to' => '#2563EB', 'bg' => 'from-indigo-50/80 via-blue-50/60 to-purple-50/40'],
        'finance' => ['from' => '#16A34A', 'via' => '#0EA5E9', 'to' => '#2563EB', 'bg' => 'from-green-50/80 via-blue-50/60 to-indigo-50/40'],
        'billing' => ['from' => '#16A34A', 'via' => '#0EA5E9', 'to' => '#2563EB', 'bg' => 'from-green-50/80 via-blue-50/60 to-indigo-50/40'],
        'accounts' => ['from' => '#4F46E5', 'via' => '#2563EB', 'to' => '#0EA5E9', 'bg' => 'from-indigo-50/80 via-blue-50/60 to-sky-50/40'],
        'institute' => ['from' => '#0EA5E9', 'via' => '#2563EB', 'to' => '#4F46E5', 'bg' => 'from-sky-50/80 via-blue-50/60 to-indigo-50/40'],
        'settings' => ['from' => '#64748B', 'via' => '#475569', 'to' => '#334155', 'bg' => 'from-slate-50/80 via-gray-50/60 to-slate-50/40'],
        'more' => ['from' => '#64748B', 'via' => '#475569', 'to' => '#334155', 'bg' => 'from-slate-50/80 via-gray-50/60 to-slate-50/40'],
    ];

    $config = $accents[$accent] ?? $accents['brand'];
    $accentFrom = $config['from'];
    $accentMid = $config['via'];
    $accentTo = $config['to'];
    $bgGradient = $config['bg'];
@endphp

<section {{ $attributes->class('relative overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-gray-200/50') }}>
    <!-- Animated gradient background -->
    <div class="absolute inset-0 bg-gradient-to-br {{ $bgGradient }} opacity-60"></div>
    
    <!-- Top gradient bar with animation -->
    <div
        class="absolute inset-x-0 top-0 h-2 animate-gradient-x"
        style="background: linear-gradient(90deg, {{ $accentFrom }}, {{ $accentMid }}, {{ $accentTo }}, {{ $accentMid }}, {{ $accentFrom }}); background-size: 200% 100%;"
    ></div>
    
    <!-- Decorative elements -->
    <div class="absolute right-0 top-0 h-64 w-64 translate-x-32 -translate-y-32 rounded-full opacity-20" style="background: radial-gradient(circle, {{ $accentFrom }}, transparent);"></div>
    <div class="absolute left-0 bottom-0 h-48 w-48 -translate-x-24 translate-y-24 rounded-full opacity-15" style="background: radial-gradient(circle, {{ $accentTo }}, transparent);"></div>
    
    <div class="relative px-8 py-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
            @php
                $leadingSlot = $leading ?? null;
                $leadingContent = $leadingSlot && ! $leadingSlot->isEmpty() ? $leadingSlot : null;
            @endphp

            <div class="flex min-w-0 items-start gap-5">
                @if ($leadingContent)
                    <div class="shrink-0">
                        {{ $leadingContent }}
                    </div>
                @endif
                <div class="min-w-0">
                    <div class="flex items-center gap-3">
                        <div class="h-1 w-12 rounded-full" style="background: linear-gradient(90deg, {{ $accentFrom }}, {{ $accentMid }});"></div>
                        <span class="text-xs font-bold uppercase tracking-widest" style="color: {{ $accentFrom }};">{{ ucfirst($accent) }}</span>
                    </div>
                    <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">
                        {{ $title }}
                    </h1>
                    @if ($subtitle)
                        <p class="mt-2 text-base text-gray-600 max-w-2xl">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            @isset($actions)
                <div class="flex flex-wrap items-center gap-3">
                    {{ $actions }}
                </div>
            @endisset
        </div>

        @isset($after)
            <div class="mt-6">
                {{ $after }}
            </div>
        @endisset
    </div>
    
    <!-- Bottom shadow effect -->
    <div class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-gray-300 to-transparent"></div>
</section>
