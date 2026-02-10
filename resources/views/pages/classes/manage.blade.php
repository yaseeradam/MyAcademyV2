@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\SchoolClass> $classes */
    $user = auth()->user();
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Manage Classes & Sections" subtitle="Edit names/levels, add sections, and remove empty records." accent="classes">
            <x-slot:actions>
                <a href="{{ route('classes.index') }}" class="btn-outline">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
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

        @if ($classes->isNotEmpty())
            <div class="space-y-6">
                @foreach ($classes as $class)
                    @php
                        $palette = [
                            ['bg' => 'from-blue-50 to-indigo-50/60', 'border' => 'border-blue-200', 'badge' => 'bg-blue-100 text-blue-700 ring-blue-200', 'button' => 'bg-blue-600 hover:bg-blue-700'],
                            ['bg' => 'from-emerald-50 to-teal-50/60', 'border' => 'border-emerald-200', 'badge' => 'bg-emerald-100 text-emerald-700 ring-emerald-200', 'button' => 'bg-emerald-600 hover:bg-emerald-700'],
                            ['bg' => 'from-purple-50 to-pink-50/60', 'border' => 'border-purple-200', 'badge' => 'bg-purple-100 text-purple-700 ring-purple-200', 'button' => 'bg-purple-600 hover:bg-purple-700'],
                            ['bg' => 'from-amber-50 to-orange-50/60', 'border' => 'border-amber-200', 'badge' => 'bg-amber-100 text-amber-700 ring-amber-200', 'button' => 'bg-amber-600 hover:bg-amber-700'],
                            ['bg' => 'from-rose-50 to-pink-50/60', 'border' => 'border-rose-200', 'badge' => 'bg-rose-100 text-rose-700 ring-rose-200', 'button' => 'bg-rose-600 hover:bg-rose-700'],
                        ];
                        $scheme = $palette[$class->id % count($palette)];
                    @endphp

                    <div class="rounded-2xl border {{ $scheme['border'] }} bg-gradient-to-br {{ $scheme['bg'] }} p-6 shadow-lg">
                        <div class="flex items-start justify-between gap-4 mb-5">
                            <div class="flex items-center gap-3">
                                <div class="grid h-12 w-12 place-items-center rounded-xl bg-white/80 backdrop-blur-sm shadow-md">
                                    <svg class="h-6 w-6 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-xl font-black text-gray-900">{{ $class->name }}</div>
                                    <div class="mt-0.5 flex items-center gap-3 text-sm">
                                        <span class="inline-flex items-center gap-1.5 rounded-full {{ $scheme['badge'] }} px-2.5 py-1 text-xs font-bold ring-1">
                                            <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="3"/></svg>
                                            Level {{ $class->level }}
                                        </span>
                                        <span class="font-semibold text-gray-600">{{ number_format((int) $class->students_count) }} students</span>
                                        <span class="font-semibold text-gray-600">{{ number_format((int) $class->sections_count) }} sections</span>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('classes.destroy', $class) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl bg-white/80 px-4 py-2 text-sm font-bold text-red-600 ring-1 ring-red-200 backdrop-blur-sm hover:bg-red-50 hover:ring-red-300 transition-all">
                                    Delete Class
                                </button>
                            </form>
                        </div>

                        <form method="POST" action="{{ route('classes.update', $class) }}" class="mb-5">
                            @csrf
                            @method('PATCH')
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                <div class="sm:col-span-2">
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Class Name</label>
                                    <input name="name" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-indigo-500" value="{{ old('name', $class->name) }}" required />
                                </div>
                                <div>
                                    <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Level</label>
                                    <input name="level" type="number" min="1" max="30" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-indigo-500" value="{{ old('level', $class->level) }}" required />
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <button type="submit" class="rounded-xl {{ $scheme['button'] }} px-5 py-2.5 text-sm font-bold text-white shadow-lg transition-all hover:shadow-xl">
                                    Save Changes
                                </button>
                            </div>
                        </form>

                        <div class="rounded-xl border border-white/60 bg-white/50 p-5 backdrop-blur-sm">
                            <div class="flex items-center justify-between mb-4">
                                <div class="text-sm font-black uppercase tracking-wider text-gray-700">Sections</div>
                                <div class="text-xs font-semibold text-gray-500">{{ $class->sections->count() }} total</div>
                            </div>

                            <div class="flex flex-wrap gap-2 mb-4">
                                @forelse ($class->sections as $section)
                                    <form method="POST" action="{{ route('sections.destroy', ['class' => $class, 'section' => $section]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-bold text-gray-700 ring-1 ring-gray-200 hover:bg-gray-50 hover:ring-gray-300 transition-all">
                                            <span>{{ $section->name }}</span>
                                            <svg class="h-4 w-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                                <path d="M18 6L6 18M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </form>
                                @empty
                                    <div class="text-sm font-semibold text-gray-500">No sections yet</div>
                                @endforelse
                            </div>

                            <form method="POST" action="{{ route('sections.store', $class) }}" class="flex flex-col gap-3 sm:flex-row">
                                @csrf
                                <div class="flex-1">
                                    <input name="name" class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-gray-200 focus:ring-2 focus:ring-indigo-500" placeholder="Section name (e.g., A, B, C)" required />
                                </div>
                                <button type="submit" class="rounded-xl {{ $scheme['button'] }} px-6 py-3 text-sm font-bold text-white shadow-lg transition-all hover:shadow-xl">
                                    Add Section
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="card-padded text-center">
                <div class="text-sm font-semibold text-slate-900">No classes yet</div>
                <div class="mt-2 text-sm text-slate-600">Create classes first from the Classes page.</div>
            </div>
        @endif
    </div>
@endsection
