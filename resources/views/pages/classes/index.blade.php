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
                        ['from' => 'from-sky-400', 'via' => 'via-blue-500', 'to' => 'to-indigo-500', 'bg' => 'bg-sky-50/40', 'ring' => 'ring-sky-200/60', 'iconBg' => 'bg-sky-100', 'iconText' => 'text-sky-700', 'accent' => 'bg-sky-500'],
                        ['from' => 'from-emerald-400', 'via' => 'via-teal-500', 'to' => 'to-cyan-500', 'bg' => 'bg-emerald-50/40', 'ring' => 'ring-emerald-200/60', 'iconBg' => 'bg-emerald-100', 'iconText' => 'text-emerald-700', 'accent' => 'bg-emerald-500'],
                        ['from' => 'from-violet-400', 'via' => 'via-indigo-500', 'to' => 'to-blue-500', 'bg' => 'bg-violet-50/40', 'ring' => 'ring-violet-200/60', 'iconBg' => 'bg-violet-100', 'iconText' => 'text-violet-700', 'accent' => 'bg-violet-500'],
                        ['from' => 'from-amber-400', 'via' => 'via-orange-500', 'to' => 'to-rose-500', 'bg' => 'bg-amber-50/40', 'ring' => 'ring-amber-200/60', 'iconBg' => 'bg-amber-100', 'iconText' => 'text-amber-700', 'accent' => 'bg-amber-500'],
                    ];
                    $scheme = $palette[$class->id % count($palette)];
                @endphp

                <div class="relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 {{ $scheme['ring'] }} transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="h-1.5 bg-gradient-to-r {{ $scheme['from'] }} {{ $scheme['via'] }} {{ $scheme['to'] }}"></div>
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full opacity-5 {{ $scheme['accent'] }}"></div>
                    <div class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="inline-flex items-center gap-2 rounded-full bg-white/80 px-2.5 py-1 text-xs font-semibold text-slate-600 ring-1 ring-gray-200/70 backdrop-blur-sm">
                                    Level {{ $class->level }}
                                </div>
                                <div class="mt-2.5 truncate text-lg font-bold tracking-tight text-slate-900">{{ $class->name }}</div>
                                <div class="mt-1 text-sm text-slate-600">Enrollment overview</div>
                            </div>
                            <div class="icon-3d grid h-14 w-14 place-items-center rounded-xl bg-gradient-to-br {{ $scheme['from'] }} {{ $scheme['to'] }} text-white shadow-lg shadow-{{ explode('-', $scheme['iconText'])[1] ?? 'blue' }}-500/30">
                                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-white/90 px-4 py-3 ring-1 ring-gray-200/70 backdrop-blur-sm">
                                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Sections</div>
                                <div class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ number_format((int) $class->sections_count) }}</div>
                            </div>
                            <div class="rounded-xl bg-white/90 px-4 py-3 ring-1 ring-gray-200/70 backdrop-blur-sm">
                                <div class="text-xs font-medium uppercase tracking-wide text-slate-500">Students</div>
                                <div class="mt-2 text-2xl font-bold tracking-tight text-slate-900">{{ number_format((int) $class->students_count) }}</div>
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
