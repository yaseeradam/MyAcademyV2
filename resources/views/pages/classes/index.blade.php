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

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($classes as $class)
                @php
                    $palette = [
                        ['from' => 'from-sky-400', 'via' => 'via-blue-500', 'to' => 'to-indigo-500', 'bg' => 'from-sky-50 to-blue-100/60', 'ring' => 'ring-sky-300/40', 'iconBg' => 'from-sky-400 to-sky-600', 'iconText' => 'text-sky-700', 'accent' => 'bg-sky-500', 'shadow' => 'shadow-sky-500/30', 'badge' => 'bg-sky-100 text-sky-700 ring-sky-200'],
                        ['from' => 'from-emerald-400', 'via' => 'via-teal-500', 'to' => 'to-cyan-500', 'bg' => 'from-emerald-50 to-teal-100/60', 'ring' => 'ring-emerald-300/40', 'iconBg' => 'from-emerald-400 to-emerald-600', 'iconText' => 'text-emerald-700', 'accent' => 'bg-emerald-500', 'shadow' => 'shadow-emerald-500/30', 'badge' => 'bg-emerald-100 text-emerald-700 ring-emerald-200'],
                        ['from' => 'from-violet-400', 'via' => 'via-indigo-500', 'to' => 'to-blue-500', 'bg' => 'from-violet-50 to-indigo-100/60', 'ring' => 'ring-violet-300/40', 'iconBg' => 'from-violet-400 to-violet-600', 'iconText' => 'text-violet-700', 'accent' => 'bg-violet-500', 'shadow' => 'shadow-violet-500/30', 'badge' => 'bg-violet-100 text-violet-700 ring-violet-200'],
                        ['from' => 'from-amber-400', 'via' => 'via-orange-500', 'to' => 'to-rose-500', 'bg' => 'from-amber-50 to-orange-100/60', 'ring' => 'ring-amber-300/40', 'iconBg' => 'from-amber-400 to-amber-600', 'iconText' => 'text-amber-700', 'accent' => 'bg-amber-500', 'shadow' => 'shadow-amber-500/30', 'badge' => 'bg-amber-100 text-amber-700 ring-amber-200'],
                        ['from' => 'from-pink-400', 'via' => 'via-rose-500', 'to' => 'to-red-500', 'bg' => 'from-pink-50 to-rose-100/60', 'ring' => 'ring-pink-300/40', 'iconBg' => 'from-pink-400 to-pink-600', 'iconText' => 'text-pink-700', 'accent' => 'bg-pink-500', 'shadow' => 'shadow-pink-500/30', 'badge' => 'bg-pink-100 text-pink-700 ring-pink-200'],
                    ];
                    $scheme = $palette[$class->id % count($palette)];
                @endphp

                <div class="group relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $scheme['bg'] }} shadow-lg ring-1 {{ $scheme['ring'] }} transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">
                    <div class="absolute inset-0 bg-gradient-to-br {{ $scheme['from'] }} {{ $scheme['via'] }} {{ $scheme['to'] }} opacity-0 transition-opacity duration-500 group-hover:opacity-10"></div>
                    <div class="h-2 bg-gradient-to-r {{ $scheme['from'] }} {{ $scheme['via'] }} {{ $scheme['to'] }}"></div>
                    <div class="absolute right-0 top-0 h-32 w-32 -translate-y-8 translate-x-8 rounded-full {{ $scheme['accent'] }} opacity-10"></div>
                    <div class="absolute left-0 bottom-0 h-24 w-24 -translate-x-6 translate-y-6 rounded-full {{ $scheme['accent'] }} opacity-5"></div>
                    
                    <div class="relative p-7">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <div class="inline-flex items-center gap-2 rounded-full {{ $scheme['badge'] }} px-3 py-1.5 text-xs font-bold uppercase tracking-wider ring-1 backdrop-blur-sm">
                                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="currentColor">
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                    Level {{ $class->level }}
                                </div>
                                <div class="mt-3 truncate text-2xl font-black tracking-tight text-slate-900">{{ $class->name }}</div>
                                <div class="mt-1.5 flex items-center gap-2 text-sm text-slate-600">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                    </svg>
                                    Academic year overview
                                </div>
                            </div>
                            <div class="icon-3d grid h-16 w-16 place-items-center rounded-2xl bg-gradient-to-br {{ $scheme['iconBg'] }} text-white shadow-xl {{ $scheme['shadow'] }} transition-transform duration-500 group-hover:rotate-12 group-hover:scale-110">
                                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="group/stat relative overflow-hidden rounded-2xl bg-white/80 px-5 py-4 ring-1 ring-white/60 backdrop-blur-sm transition-all duration-300 hover:bg-white hover:shadow-lg">
                                <div class="absolute inset-0 bg-gradient-to-br {{ $scheme['from'] }} {{ $scheme['to'] }} opacity-0 transition-opacity duration-300 group-hover/stat:opacity-5"></div>
                                <div class="relative">
                                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider {{ $scheme['iconText'] }}">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                        </svg>
                                        Sections
                                    </div>
                                    <div class="mt-2 text-3xl font-black tracking-tight text-slate-900">{{ number_format((int) $class->sections_count) }}</div>
                                </div>
                            </div>
                            <div class="group/stat relative overflow-hidden rounded-2xl bg-white/80 px-5 py-4 ring-1 ring-white/60 backdrop-blur-sm transition-all duration-300 hover:bg-white hover:shadow-lg">
                                <div class="absolute inset-0 bg-gradient-to-br {{ $scheme['from'] }} {{ $scheme['to'] }} opacity-0 transition-opacity duration-300 group-hover/stat:opacity-5"></div>
                                <div class="relative">
                                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider {{ $scheme['iconText'] }}">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                        </svg>
                                        Students
                                    </div>
                                    <div class="mt-2 text-3xl font-black tracking-tight text-slate-900">{{ number_format((int) $class->students_count) }}</div>
                                </div>
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
