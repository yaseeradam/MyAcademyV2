<div class="space-y-6">
    <div class="rounded-2xl border border-blue-100 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">All Students</h1>
                <p class="mt-1 text-sm text-gray-600">Manage, search and filter student records</p>
            </div>
            <div class="flex gap-3">
                <x-export type="students" :filters="[
        'class' => $this->classFilter,
        'section' => $this->sectionFilter,
        'status' => $this->statusFilter,
        'search' => $this->search,
    ]" />
                @if (auth()->user()?->role === 'admin')
                    <a href="{{ route('students.create') }}" class="btn-primary">Add Student</a>
                @endif
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-gray-200 bg-white p-4">
            <div class="grid grid-cols-1 gap-3 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-2">
                    <select wire:model.live="classFilter" class="select">
                        <option value="all">All Classes</option>
                        @foreach ($this->classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select wire:model.live="sectionFilter" class="select">
                        <option value="all">All Sections</option>
                        @foreach ($this->sections as $section)
                            @if ($this->classFilter === 'all')
                                <option value="{{ $section }}">{{ $section }}</option>
                            @else
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select wire:model.live="statusFilter" class="select">
                        <option value="all">Status</option>
                        <option value="Active">Active</option>
                        <option value="Graduated">Graduated</option>
                        <option value="Expelled">Expelled</option>
                    </select>
                </div>

                <div class="lg:col-span-6">
                    <input wire:model.live.debounce.300ms="search" type="text"
                        placeholder="Search by name, admission number, class, parent..." class="input" />
                </div>
            </div>
        </div>
    </div>

    @if (session('status'))
        <x-alert type="success" :message="session('status')" />
    @endif

    @if (session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <x-stat-card label="Total Students" :value="$this->stats['total']" cardBg="bg-sky-50/60"
            ringColor="ring-sky-100" iconBg="bg-blue-50" iconColor="text-blue-600">
            <x-slot:icon>
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-3-3.87" />
                    <path d="M7 21v-2a4 4 0 0 1 3-3.87" />
                    <path d="M12 7a4 4 0 1 0-8 0 4 4 0 0 0 8 0z" />
                    <path d="M20 7a3 3 0 1 0-6 0 3 3 0 0 0 6 0z" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="Boys" :value="$this->stats['boys']" cardBg="bg-emerald-50/60" ringColor="ring-emerald-100"
            iconBg="bg-emerald-50" iconColor="text-emerald-700">
            <x-slot:icon>
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="10" cy="14" r="5" />
                    <path d="M13.5 10.5 21 3" />
                    <path d="M16 3h5v5" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="Girls" :value="$this->stats['girls']" cardBg="bg-violet-50/60" ringColor="ring-violet-100"
            iconBg="bg-violet-50" iconColor="text-violet-700">
            <x-slot:icon>
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="9" r="5" />
                    <path d="M12 14v7" />
                    <path d="M9 18h6" />
                </svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card label="Alumni" :value="$this->stats['alumni']" cardBg="bg-amber-50/60" ringColor="ring-amber-100"
            iconBg="bg-amber-50" iconColor="text-amber-700">
            <x-slot:icon>
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 10 12 5 2 10l10 5 10-5z" />
                    <path d="M6 12v5a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-5" />
                    <path d="M2 10v6" />
                </svg>
            </x-slot:icon>
        </x-stat-card>
    </div>


    <x-table sortable selectable :items="$this->students">
        <thead
            class="bg-white text-xs font-semibold uppercase tracking-wider text-gray-900 border-b-2 border-gray-300">
            <tr>
                <th class="px-5 py-3">
                    <input type="checkbox" class="checkbox-custom" value="all" />
                </th>
                <th class="px-5 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('last_name')">
                    <div class="flex items-center gap-1">
                        Student
                        @if($sortBy === 'last_name')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                        @endif
                    </div>
                </th>
                <th class="px-5 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('admission_number')">
                    <div class="flex items-center gap-1">
                        Admission No
                        @if($sortBy === 'admission_number')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                        @endif
                    </div>
                </th>
                <th class="px-5 py-3">Class / Section</th>
                <th class="px-5 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('gender')">
                    <div class="flex items-center gap-1">
                        Gender
                        @if($sortBy === 'gender')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                        @endif
                    </div>
                </th>
                <th class="px-5 py-3">Guardian</th>
                <th class="px-5 py-3 cursor-pointer hover:bg-gray-100" wire:click="sortBy('status')">
                    <div class="flex items-center gap-1">
                        Status
                        @if($sortBy === 'status')
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                @if($sortDir === 'asc')
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                                @else
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                @endif
                            </svg>
                        @endif
                    </div>
                </th>
                <th class="px-5 py-3 text-right">Profile</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($this->students as $student)
                @php
                    $initials = collect(explode(' ', $student->full_name))
                        ->filter()
                        ->map(fn($part) => mb_substr($part, 0, 1))
                        ->take(2)
                        ->implode('');
                @endphp
                <tr class="bg-white hover:bg-gray-50 animate-fade-in" wire:loading.class="opacity-50">
                    <td class="px-5 py-4">
                        <input type="checkbox" class="checkbox-custom" value="{{ $student->id }}" />
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            @if($student->passport_photo_url)
                                <img src="{{ $student->passport_photo_url }}" alt="{{ $student->full_name }}"
                                    class="h-10 w-10 rounded-full object-cover ring-2 ring-gray-100">
                            @else
                                <div
                                    class="grid h-10 w-10 place-items-center rounded-full bg-brand-50 text-sm font-bold text-brand-600">
                                    {{ $initials }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-gray-900">{{ $student->full_name }}</div>
                                <div class="truncate text-xs text-gray-500">
                                    {{ $student->schoolClass?->name }} • {{ $student->section?->name }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm font-medium text-gray-700">{{ $student->admission_number }}</td>
                    <td class="px-5 py-4 text-sm text-gray-700">
                        {{ $student->schoolClass?->name }} / {{ $student->section?->name }}
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-700">{{ $student->gender }}</td>
                    <td class="px-5 py-4 text-sm text-gray-700">
                        <div class="font-medium text-gray-800">{{ $student->guardian_name ?: '—' }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ $student->guardian_phone ?: '' }}</div>
                    </td>
                    <td class="px-5 py-4">
                        @php
                            $variant = match ($student->status) {
                                'Active' => 'success',
                                'Graduated' => 'info',
                                default => 'warning',
                            };
                        @endphp
                        <x-status-badge variant="{{ $variant }}">{{ $student->status }}</x-status-badge>
                    </td>
                    <td class="px-5 py-4 text-right">
                        <a class="text-sm font-semibold text-brand-600 hover:text-brand-700"
                            href="{{ route('students.show', ['student' => $student]) }}">
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-12">
                        <x-empty-state icon="users" title="No students found"
                            message="No students match your current filters. Try adjusting your search criteria or add a new student."
                            :action="auth()->user()?->role === 'admin' ? route('students.create') : null"
                            :actionText="auth()->user()?->role === 'admin' ? 'Add Student' : null" />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="pt-4">
        {{ $this->students->links() }}
    </div>
</div>