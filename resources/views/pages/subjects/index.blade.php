@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\Subject> $subjects */
    $user = auth()->user();
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Subjects" subtitle="Create subject codes used for results and allocations." accent="subjects">
            <x-slot:actions>
                <a href="{{ route('classes.index') }}" class="btn-outline">Classes</a>
            </x-slot:actions>
        </x-page-header>

        @if (session('status'))
            <div class="card-padded border border-green-200 bg-green-50/60 text-sm text-green-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="card-padded border border-orange-200 bg-orange-50/60">
                <div class="text-sm font-semibold text-orange-900">Please fix the following:</div>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-orange-900">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($user?->role === 'admin')
            <form method="POST" action="{{ route('subjects.store') }}" class="card-padded">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-slate-900">Subject name</label>
                        <div class="mt-2">
                            <input name="name" class="input" value="{{ old('name') }}" placeholder="e.g., Mathematics" required />
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-900">Code</label>
                        <div class="mt-2">
                            <input name="code" class="input" value="{{ old('code') }}" placeholder="e.g., MATH" required />
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Add Subject
                    </button>
                </div>
            </form>
        @endif

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse ($subjects as $subject)
                @php
                    $colors = [
                        ['bg' => 'from-blue-50 to-indigo-100/60', 'ring' => 'ring-blue-300/40', 'icon' => 'from-blue-400 to-blue-600', 'shadow' => 'shadow-blue-500/30', 'badge' => 'bg-blue-100 text-blue-700 ring-blue-200', 'accent' => 'bg-blue-500'],
                        ['bg' => 'from-purple-50 to-violet-100/60', 'ring' => 'ring-purple-300/40', 'icon' => 'from-purple-400 to-purple-600', 'shadow' => 'shadow-purple-500/30', 'badge' => 'bg-purple-100 text-purple-700 ring-purple-200', 'accent' => 'bg-purple-500'],
                        ['bg' => 'from-emerald-50 to-green-100/60', 'ring' => 'ring-emerald-300/40', 'icon' => 'from-emerald-400 to-emerald-600', 'shadow' => 'shadow-emerald-500/30', 'badge' => 'bg-emerald-100 text-emerald-700 ring-emerald-200', 'accent' => 'bg-emerald-500'],
                        ['bg' => 'from-orange-50 to-amber-100/60', 'ring' => 'ring-orange-300/40', 'icon' => 'from-orange-400 to-orange-600', 'shadow' => 'shadow-orange-500/30', 'badge' => 'bg-orange-100 text-orange-700 ring-orange-200', 'accent' => 'bg-orange-500'],
                        ['bg' => 'from-pink-50 to-rose-100/60', 'ring' => 'ring-pink-300/40', 'icon' => 'from-pink-400 to-pink-600', 'shadow' => 'shadow-pink-500/30', 'badge' => 'bg-pink-100 text-pink-700 ring-pink-200', 'accent' => 'bg-pink-500'],
                        ['bg' => 'from-cyan-50 to-teal-100/60', 'ring' => 'ring-cyan-300/40', 'icon' => 'from-cyan-400 to-cyan-600', 'shadow' => 'shadow-cyan-500/30', 'badge' => 'bg-cyan-100 text-cyan-700 ring-cyan-200', 'accent' => 'bg-cyan-500'],
                    ];
                    $color = $colors[$subject->id % count($colors)];
                @endphp

                <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $color['bg'] }} shadow-lg ring-1 {{ $color['ring'] }} transition-all duration-500 hover:shadow-2xl hover:-translate-y-2">
                    <div class="absolute right-0 top-0 h-24 w-24 -translate-y-6 translate-x-6 rounded-full {{ $color['accent'] }} opacity-10"></div>
                    <div class="absolute left-0 bottom-0 h-16 w-16 -translate-x-4 translate-y-4 rounded-full {{ $color['accent'] }} opacity-5"></div>
                    
                    <div class="relative p-6">
                        <div class="flex items-start justify-between gap-3">
                            <div class="icon-3d grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br {{ $color['icon'] }} text-white shadow-xl {{ $color['shadow'] }} transition-transform duration-500 group-hover:rotate-12 group-hover:scale-110">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                </svg>
                            </div>
                            <div class="inline-flex items-center gap-1.5 rounded-full {{ $color['badge'] }} px-3 py-1.5 text-xs font-black uppercase tracking-wider ring-1">
                                {{ $subject->code }}
                            </div>
                        </div>

                        <div class="mt-5">
                            @if ($user?->role === 'admin')
                                <form id="subject-update-{{ $subject->id }}" method="POST" action="{{ route('subjects.update', $subject) }}" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <input
                                        name="name"
                                        class="w-full rounded-xl border-0 bg-white/80 px-4 py-2.5 text-base font-bold text-slate-900 ring-1 ring-white/60 backdrop-blur-sm transition-all focus:bg-white focus:ring-2 focus:ring-white"
                                        value="{{ old('name', $subject->name) }}"
                                        required
                                    />
                                    <div class="flex gap-2">
                                        <input
                                            name="code"
                                            class="w-24 rounded-xl border-0 bg-white/80 px-3 py-2 text-sm font-bold text-slate-900 ring-1 ring-white/60 backdrop-blur-sm transition-all focus:bg-white focus:ring-2 focus:ring-white"
                                            value="{{ old('code', $subject->code) }}"
                                            required
                                        />
                                        <button type="submit" class="flex-1 rounded-xl bg-white/90 px-4 py-2 text-sm font-bold text-slate-900 ring-1 ring-white/60 backdrop-blur-sm transition-all hover:bg-white hover:shadow-lg">
                                            Save
                                        </button>
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('subjects.destroy', $subject) }}" class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full rounded-xl bg-red-500/10 px-4 py-2 text-sm font-bold text-red-600 ring-1 ring-red-200/50 backdrop-blur-sm transition-all hover:bg-red-500/20">
                                        Delete
                                    </button>
                                </form>
                            @else
                                <div class="text-xl font-black tracking-tight text-slate-900">{{ $subject->name }}</div>
                                <div class="mt-2 text-sm text-slate-600">Subject code: {{ $subject->code }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-3xl bg-gradient-to-br from-slate-50 to-gray-100/60 p-12 text-center shadow-lg ring-1 ring-slate-200/50">
                    <div class="mx-auto grid h-20 w-20 place-items-center rounded-2xl bg-gradient-to-br from-slate-400 to-slate-600 text-white shadow-xl">
                        <svg class="h-10 w-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </div>
                    <div class="mt-6 text-xl font-black text-slate-900">No subjects yet</div>
                    <div class="mt-2 text-sm text-slate-600">Add your first subject to get started</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
