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

        <div class="card-padded">
            <div class="flex items-center justify-between gap-4">
                <div class="text-sm font-semibold text-slate-900">All Subjects</div>
                <div class="text-xs text-slate-500">{{ number_format((int) $subjects->count()) }} total</div>
            </div>

            <div class="mt-4">
                <x-table>
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Subject</th>
                            <th class="px-5 py-3">Code</th>
                            @if ($user?->role === 'admin')
                                <th class="px-5 py-3 text-right">Action</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($subjects as $subject)
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    @if ($user?->role === 'admin')
                                        <form id="subject-update-{{ $subject->id }}" method="POST" action="{{ route('subjects.update', $subject) }}" class="hidden">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                        <input
                                            form="subject-update-{{ $subject->id }}"
                                            name="name"
                                            class="input-compact w-full"
                                            value="{{ old('name', $subject->name) }}"
                                            required
                                        />
                                    @else
                                        <div class="text-sm font-semibold text-slate-900">{{ $subject->name }}</div>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    @if ($user?->role === 'admin')
                                        <div class="flex items-center justify-end gap-2">
                                            <input
                                                form="subject-update-{{ $subject->id }}"
                                                name="code"
                                                class="input-compact w-28"
                                                value="{{ old('code', $subject->code) }}"
                                                required
                                            />
                                            <button form="subject-update-{{ $subject->id }}" type="submit" class="btn-outline">Save</button>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-inset ring-slate-200">
                                            {{ $subject->code }}
                                        </span>
                                    @endif
                                </td>
                                @if ($user?->role === 'admin')
                                    <td class="px-5 py-4 text-right">
                                        <form method="POST" action="{{ route('subjects.destroy', $subject) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ghost">Delete</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $user?->role === 'admin' ? 3 : 2 }}" class="px-5 py-10 text-center text-sm text-slate-500">No subjects yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </div>
        </div>
    </div>
@endsection
