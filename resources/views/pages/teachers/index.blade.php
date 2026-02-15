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
        <div class="rounded-2xl border border-orange-100 bg-gradient-to-br from-orange-50 to-amber-50 p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Teachers</h1>
                    <p class="mt-1 text-sm text-gray-600">Teaching staff directory and subject coverage</p>
                </div>
                @if (auth()->user()?->role === 'admin')
                    <a href="{{ route('teachers.create') }}" class="btn-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Add Teacher
                    </a>
                @endif
            </div>

            <div class="mt-6 rounded-xl border border-gray-200 bg-white p-4">
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-4">
                    <input type="text" id="teacherSearch" placeholder="Search by name or email..." class="lg:col-span-3 input" />
                    <select id="statusFilter" class="select">
                        <option value="all">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="card-padded border border-green-200 bg-green-50/60 text-sm text-green-900">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
            @forelse ($teachers as $teacher)
                @php
                    $rows = $allocations->get($teacher->id, collect());
                    $colors = [
                        ['bg' => 'from-amber-50 to-orange-100/60', 'ring' => 'ring-amber-300/40', 'icon' => 'from-amber-400 to-amber-600', 'shadow' => 'shadow-amber-500/30', 'badge' => 'bg-amber-100 text-amber-700 ring-amber-200', 'accent' => 'bg-amber-500'],
                        ['bg' => 'from-blue-50 to-indigo-100/60', 'ring' => 'ring-blue-300/40', 'icon' => 'from-blue-400 to-blue-600', 'shadow' => 'shadow-blue-500/30', 'badge' => 'bg-blue-100 text-blue-700 ring-blue-200', 'accent' => 'bg-blue-500'],
                        ['bg' => 'from-purple-50 to-violet-100/60', 'ring' => 'ring-purple-300/40', 'icon' => 'from-purple-400 to-purple-600', 'shadow' => 'shadow-purple-500/30', 'badge' => 'bg-purple-100 text-purple-700 ring-purple-200', 'accent' => 'bg-purple-500'],
                        ['bg' => 'from-emerald-50 to-teal-100/60', 'ring' => 'ring-emerald-300/40', 'icon' => 'from-emerald-400 to-emerald-600', 'shadow' => 'shadow-emerald-500/30', 'badge' => 'bg-emerald-100 text-emerald-700 ring-emerald-200', 'accent' => 'bg-emerald-500'],
                        ['bg' => 'from-pink-50 to-rose-100/60', 'ring' => 'ring-pink-300/40', 'icon' => 'from-pink-400 to-pink-600', 'shadow' => 'shadow-pink-500/30', 'badge' => 'bg-pink-100 text-pink-700 ring-pink-200', 'accent' => 'bg-pink-500'],
                    ];
                    $color = $colors[$teacher->id % count($colors)];
                @endphp
                <a href="{{ route('teachers.show', $teacher) }}" class="group relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $color['bg'] }} shadow-lg ring-1 {{ $color['ring'] }} transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 hover:scale-[1.02]">
                    <div class="h-28 bg-gradient-to-br from-white/20 to-transparent"></div>
                    <div class="absolute right-6 top-6 h-20 w-20 rounded-full bg-white/20"></div>
                    <div class="absolute left-0 bottom-0 h-32 w-32 -translate-x-8 translate-y-8 rounded-full {{ $color['accent'] }} opacity-10"></div>
                    
                    <div class="-mt-16 px-6">
                        <div class="flex items-start gap-4">
                            @if ($teacher->profile_photo_url)
                                <img
                                    src="{{ $teacher->profile_photo_url }}"
                                    alt="{{ $teacher->name }}"
                                    class="h-24 w-24 rounded-2xl object-cover ring-4 ring-white shadow-xl transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3"
                                />
                            @else
                                <div class="grid h-24 w-24 place-items-center rounded-2xl bg-gradient-to-br {{ $color['icon'] }} text-white shadow-xl {{ $color['shadow'] }} ring-4 ring-white transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">
                                    <span class="text-3xl font-black">{{ mb_substr($teacher->name, 0, 1) }}</span>
                                </div>
                            @endif

                            <div class="min-w-0 flex-1 pt-6">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="truncate text-lg font-black text-slate-900">{{ $teacher->name }}</div>
                                        <div class="mt-1 truncate text-sm text-slate-600">{{ $teacher->email }}</div>
                                    </div>
                                    <x-status-badge variant="{{ $teacher->is_active ? 'success' : 'warning' }}">
                                        {{ $teacher->is_active ? 'Active' : 'Inactive' }}
                                    </x-status-badge>
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 rounded-2xl bg-white/80 px-4 py-3 ring-1 ring-white/60 backdrop-blur-sm">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider {{ str_replace('bg-', 'text-', $color['accent']) }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                    </svg>
                                    Allocations
                                </div>
                                <div class="text-2xl font-black text-slate-900">{{ number_format((int) $rows->count()) }}</div>
                            </div>
                        </div>

                        <div class="mt-4 pb-6">
                            @if ($rows->isEmpty())
                                <div class="rounded-2xl bg-white/60 px-4 py-3 text-center text-sm font-semibold text-slate-600 ring-1 ring-white/40 backdrop-blur-sm">
                                    No allocations yet
                                </div>
                            @else
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($rows->take(6) as $alloc)
                                        <span class="inline-flex items-center rounded-xl {{ $color['badge'] }} px-3 py-1.5 text-xs font-bold ring-1">
                                            {{ $alloc->subject?->code ?? 'SUB' }} · {{ $alloc->schoolClass?->name ?? 'Class' }}
                                        </span>
                                    @endforeach
                                    @if ($rows->count() > 6)
                                        <span class="inline-flex items-center rounded-xl bg-white/80 px-3 py-1.5 text-xs font-bold text-slate-700 ring-1 ring-white/60">+{{ $rows->count() - 6 }}</span>
                                    @endif
                                </div>
                            @endif
                        </div>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('teacherSearch');
    const statusFilter = document.getElementById('statusFilter');
    const teacherCards = document.querySelectorAll('.grid > a');

    function filterTeachers() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;

        teacherCards.forEach(card => {
            const name = card.querySelector('.text-lg')?.textContent.toLowerCase() || '';
            const email = card.querySelector('.text-sm.text-slate-600')?.textContent.toLowerCase() || '';
            const statusBadge = card.querySelector('[class*="status-badge"]')?.textContent.toLowerCase() || '';
            
            const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
            const matchesStatus = statusValue === 'all' || 
                (statusValue === 'active' && statusBadge.includes('active')) ||
                (statusValue === 'inactive' && statusBadge.includes('inactive'));

            card.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterTeachers);
    statusFilter?.addEventListener('change', filterTeachers);
});
</script>
@endpush
