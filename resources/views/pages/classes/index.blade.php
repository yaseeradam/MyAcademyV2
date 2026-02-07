@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\SchoolClass> $classes */
    $user = auth()->user();
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Classes" subtitle="Manage class levels and enrollment structure." accent="classes">
            <x-slot:actions>
                <a href="{{ route('subjects.index') }}" class="btn-outline">Subjects</a>
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
            <form method="POST" action="{{ route('classes.store') }}" class="card-padded">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="sm:col-span-2">
                        <label class="text-sm font-semibold text-slate-900">Class name</label>
                        <div class="mt-2">
                            <input name="name" class="input" value="{{ old('name') }}" placeholder="e.g., JSS 1A" required />
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-slate-900">Level</label>
                        <div class="mt-2">
                            <input name="level" type="number" class="input" value="{{ old('level', 1) }}" min="1" max="30" required />
                        </div>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Add Class
                    </button>
                </div>
            </form>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($classes as $class)
                @php
                    $palette = [
                        ['from' => 'from-sky-500', 'via' => 'via-blue-500', 'to' => 'to-indigo-500', 'bg' => 'bg-sky-50/50', 'ring' => 'ring-sky-100', 'iconBg' => 'bg-sky-100', 'iconText' => 'text-sky-800'],
                        ['from' => 'from-emerald-500', 'via' => 'via-teal-500', 'to' => 'to-cyan-500', 'bg' => 'bg-emerald-50/45', 'ring' => 'ring-emerald-100', 'iconBg' => 'bg-emerald-100', 'iconText' => 'text-emerald-800'],
                        ['from' => 'from-violet-500', 'via' => 'via-indigo-500', 'to' => 'to-blue-500', 'bg' => 'bg-violet-50/45', 'ring' => 'ring-violet-100', 'iconBg' => 'bg-violet-100', 'iconText' => 'text-violet-800'],
                        ['from' => 'from-amber-500', 'via' => 'via-orange-500', 'to' => 'to-rose-500', 'bg' => 'bg-amber-50/45', 'ring' => 'ring-amber-100', 'iconBg' => 'bg-amber-100', 'iconText' => 'text-amber-800'],
                    ];
                    $scheme = $palette[$class->id % count($palette)];
                @endphp

                <div class="card overflow-hidden ring-1 ring-inset {{ $scheme['ring'] }} {{ $scheme['bg'] }}">
                    <div class="h-1 bg-gradient-to-r {{ $scheme['from'] }} {{ $scheme['via'] }} {{ $scheme['to'] }}"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="inline-flex items-center gap-2 rounded-full bg-white/70 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-inset ring-gray-200/60">
                                    Level {{ $class->level }}
                                </div>
                                <div class="mt-2 truncate text-lg font-semibold tracking-tight text-slate-900">{{ $class->name }}</div>
                                <div class="mt-1 text-sm text-slate-600">Enrollment and sections overview.</div>
                            </div>
                            <div class="grid h-12 w-12 place-items-center rounded-2xl {{ $scheme['iconBg'] }} {{ $scheme['iconText'] }} ring-1 ring-inset ring-white/60">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4 4h16v16H4z" />
                                    <path d="M8 8h8" />
                                    <path d="M8 12h8" />
                                    <path d="M8 16h5" />
                                </svg>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-white/80 px-4 py-3 ring-1 ring-inset ring-gray-200/60">
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sections</div>
                                <div class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ number_format((int) $class->sections_count) }}</div>
                            </div>
                            <div class="rounded-2xl bg-white/80 px-4 py-3 ring-1 ring-inset ring-gray-200/60">
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Students</div>
                                <div class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">{{ number_format((int) $class->students_count) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card-padded sm:col-span-2 xl:col-span-3 text-center">
                    <div class="text-sm font-semibold text-slate-900">No classes yet</div>
                    <div class="mt-2 text-sm text-slate-600">Create your first class to begin admissions and allocations.</div>
                </div>
            @endforelse
        </div>

        @if ($user?->role === 'admin' && $classes->isNotEmpty())
            <div class="card-padded">
                <div class="flex items-center justify-between gap-4">
                    <div class="text-sm font-semibold text-slate-900">Manage Classes & Sections</div>
                    <div class="text-xs text-slate-500">Edit names/levels, add sections, and remove empty records.</div>
                </div>

                <div class="mt-4">
                    <x-table>
                        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-5 py-3">Class</th>
                                <th class="px-5 py-3 text-right">Counts</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($classes as $class)
                                <tr class="bg-white hover:bg-gray-50">
                                    <td class="px-5 py-4">
                                        <form method="POST" action="{{ route('classes.update', $class) }}" class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                                            @csrf
                                            @method('PATCH')
                                            <div class="sm:col-span-2">
                                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Name</label>
                                                <input name="name" class="mt-2 input-compact" value="{{ old('name', $class->name) }}" required />
                                            </div>
                                            <div>
                                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Level</label>
                                                <input name="level" type="number" min="1" max="30" class="mt-2 input-compact" value="{{ old('level', $class->level) }}" required />
                                            </div>
                                            <div class="sm:col-span-3 flex justify-end">
                                                <button type="submit" class="btn-outline">Save</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4 text-right text-sm text-slate-700">
                                        <div class="font-semibold text-slate-900">{{ number_format((int) $class->students_count) }}</div>
                                        <div class="mt-1 text-xs text-slate-500">{{ number_format((int) $class->sections_count) }} sections</div>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <form method="POST" action="{{ route('classes.destroy', $class) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost">Delete</button>
                                        </form>
                                    </td>
                                </tr>

                                <tr class="bg-slate-50">
                                    <td colspan="3" class="px-5 py-4">
                                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Sections</div>

                                        <div class="mt-3 flex flex-wrap gap-2">
                                            @forelse ($class->sections as $section)
                                                <form method="POST" action="{{ route('sections.destroy', ['class' => $class, 'section' => $section]) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 hover:bg-slate-50">
                                                        <span>{{ $section->name }}</span>
                                                        <span class="text-slate-400">×</span>
                                                    </button>
                                                </form>
                                            @empty
                                                <div class="text-sm text-slate-600">No sections yet.</div>
                                            @endforelse
                                        </div>

                                        <form method="POST" action="{{ route('sections.store', $class) }}" class="mt-4 flex flex-col gap-2 sm:flex-row sm:items-end">
                                            @csrf
                                            <div class="w-full sm:max-w-xs">
                                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-500">Add Section</label>
                                                <input name="name" class="mt-2 input-compact" placeholder="e.g., A" required />
                                            </div>
                                            <button type="submit" class="btn-primary w-full justify-center sm:w-auto">Add</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </x-table>
                </div>
            </div>
        @endif
    </div>
@endsection
