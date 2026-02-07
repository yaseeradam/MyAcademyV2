@php
    use App\Models\SubjectAllocation;
    use App\Models\User;

    $teachers = User::query()
        ->where('role', 'teacher')
        ->orderBy('name')
        ->get();

    $allocations = SubjectAllocation::query()
        ->with(['subject', 'schoolClass'])
        ->whereIn('teacher_id', $teachers->pluck('id'))
        ->get()
        ->groupBy('teacher_id');
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Teachers" subtitle="Teaching staff directory and subject coverage." accent="teachers">
            <x-slot:actions>
                @if (auth()->user()?->role === 'admin')
                    <a href="{{ route('teachers.create') }}" class="btn-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Add Teacher
                    </a>
                @endif
            </x-slot:actions>
        </x-page-header>

        @if (session('status'))
            <div class="card-padded border border-green-200 bg-green-50/60 text-sm text-green-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($teachers as $teacher)
                @php($rows = $allocations->get($teacher->id, collect()))
                <a href="{{ route('teachers.show', $teacher) }}" class="group relative overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="h-20 bg-gradient-to-br from-slate-50 via-gray-50 to-slate-100"></div>
                    <div class="absolute right-4 top-4 h-16 w-16 rounded-full bg-white/40 opacity-60 backdrop-blur-sm"></div>
                    <div class="-mt-10 flex items-start gap-4 px-6">
                        @if ($teacher->profile_photo_url)
                            <img
                                src="{{ $teacher->profile_photo_url }}"
                                alt="{{ $teacher->name }}"
                                class="h-20 w-20 rounded-2xl object-cover ring-4 ring-white shadow-md"
                            />
                        @else
                            <x-avatar :name="$teacher->name" size="80" class="ring-4 ring-white shadow-md" />
                        @endif

                        <div class="min-w-0 flex-1 pt-10">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-bold text-slate-900">{{ $teacher->name }}</div>
                                    <div class="truncate text-xs text-slate-500">{{ $teacher->email }}</div>
                                </div>
                                <x-status-badge variant="{{ $teacher->is_active ? 'success' : 'warning' }}">
                                    {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                </x-status-badge>
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-4 rounded-lg bg-slate-50 px-3 py-2">
                                <div class="text-xs font-medium text-slate-500">Allocations</div>
                                <div class="text-sm font-bold text-slate-900">{{ number_format((int) $rows->count()) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 pb-6 pt-4">
                        @if ($rows->isEmpty())
                            <div class="rounded-xl bg-slate-50 px-3 py-2.5 text-sm text-slate-600 ring-1 ring-slate-200">
                                No subject allocations yet.
                            </div>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($rows->take(8) as $alloc)
                                    <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                        {{ $alloc->subject?->code ?? 'SUB' }} · {{ $alloc->schoolClass?->name ?? 'Class' }}
                                    </span>
                                @endforeach
                                @if ($rows->count() > 8)
                                    <span class="text-xs font-semibold text-slate-500">+{{ $rows->count() - 8 }} more</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>
            @empty
                <div class="card-padded sm:col-span-2 xl:col-span-3">
                    <div class="text-sm font-semibold text-slate-900">No teachers found</div>
                    <div class="mt-1 text-sm text-slate-600">Create your first teacher account to begin allocations.</div>
                    @if (auth()->user()?->role === 'admin')
                        <div class="mt-4">
                            <a href="{{ route('teachers.create') }}" class="btn-primary">Add Teacher</a>
                        </div>
                    @endif
                </div>
            @endforelse
        </div>
    </div>
@endsection
