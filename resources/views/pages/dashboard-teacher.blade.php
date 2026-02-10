@php
    use App\Models\Announcement;
    use App\Models\SchoolClass;
    use App\Models\Student;
    use App\Models\SubjectAllocation;

    $user = auth()->user();
    $classIds = SubjectAllocation::query()
        ->where('teacher_id', $user->id)
        ->pluck('class_id')
        ->unique()
        ->values();

    $subjectIds = SubjectAllocation::query()
        ->where('teacher_id', $user->id)
        ->pluck('subject_id')
        ->unique()
        ->values();

    $classes = $classIds->isEmpty()
        ? collect()
        : SchoolClass::query()
            ->whereIn('id', $classIds)
            ->withCount('students')
            ->orderBy('level')
            ->orderBy('name')
            ->get();

    $studentsCount = $classIds->isEmpty()
        ? 0
        : Student::query()
            ->whereIn('class_id', $classIds)
            ->where('status', 'Active')
            ->count();

    $subjectsCount = (int) $subjectIds->count();

    $pendingSubmissions = 0;

    $announcements = Announcement::query()
        ->whereNotNull('published_at')
        ->where(function ($q) use ($user) {
            $q->where('audience', 'all')
                ->orWhere('audience', 'staff')
                ->orWhere('audience', $user->role);
        })
        ->orderByDesc('published_at')
        ->limit(4)
        ->get();

    $recentStudents = $classIds->isEmpty()
        ? collect()
        : Student::query()
            ->whereIn('class_id', $classIds)
            ->with('schoolClass')
            ->orderBy('last_name')
            ->limit(6)
            ->get();
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Teacher Dashboard" subtitle="Your classes, students, and updates." accent="teachers">
            <x-slot:actions>
                <a href="{{ route('results.entry') }}" class="btn-primary">Enter Scores</a>
                <a href="{{ route('messages') }}" class="btn-outline">Messages</a>
            </x-slot:actions>
        </x-page-header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card
                label="Assigned Classes"
                :value="number_format((int) $classes->count())"
                cardBg="bg-blue-50/60"
                ringColor="ring-blue-100"
                iconBg="bg-blue-50"
                iconColor="text-blue-600"
            >
                <x-slot:icon>
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                label="Active Students"
                :value="number_format((int) $studentsCount)"
                cardBg="bg-emerald-50/60"
                ringColor="ring-emerald-100"
                iconBg="bg-emerald-50"
                iconColor="text-emerald-700"
            >
                <x-slot:icon>
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                label="Assigned Subjects"
                :value="number_format((int) $subjectsCount)"
                cardBg="bg-violet-50/60"
                ringColor="ring-violet-100"
                iconBg="bg-violet-50"
                iconColor="text-violet-700"
            >
                <x-slot:icon>
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                        <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card
                label="Pending Submissions"
                :value="number_format((int) $pendingSubmissions)"
                cardBg="bg-amber-50/60"
                ringColor="ring-amber-100"
                iconBg="bg-amber-50"
                iconColor="text-amber-700"
            >
                <x-slot:icon>
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 16 14"/>
                    </svg>
                </x-slot:icon>
            </x-stat-card>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="space-y-4 lg:col-span-2">
                <div class="card-padded">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">My Classes</div>
                            <div class="mt-1 text-sm text-gray-600">Classes assigned to you this term.</div>
                        </div>
                        <a href="{{ route('classes.index') }}" class="btn-outline">View All</a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @forelse ($classes as $class)
                        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                            <div class="text-sm font-semibold text-gray-900">{{ $class->name }}</div>
                            <div class="mt-1 text-xs text-gray-500">Level {{ $class->level }}</div>
                            <div class="mt-4 flex items-center justify-between text-sm">
                                <span class="text-gray-600">Students</span>
                                <span class="font-semibold text-gray-900">{{ number_format((int) $class->students_count) }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-600 sm:col-span-2">
                            No classes assigned yet. Contact admin to add allocations.
                        </div>
                    @endforelse
                </div>

                <div class="card-padded">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-900">Recent Students</div>
                            <div class="mt-1 text-sm text-gray-600">Latest students from your classes.</div>
                        </div>
                        <a href="{{ route('students.index') }}" class="btn-outline">View Students</a>
                    </div>
                </div>

                <x-table>
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3">Student</th>
                            <th class="px-5 py-3">Class</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Profile</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($recentStudents as $student)
                            <tr class="bg-white hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <div class="text-sm font-semibold text-gray-900">{{ $student->full_name }}</div>
                                    <div class="mt-1 text-xs text-gray-500">{{ $student->admission_number }}</div>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700">{{ $student->schoolClass?->name ?? '-' }}</td>
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
                                    <a href="{{ route('students.show', $student) }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500">No students to show.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </x-table>
            </div>

            <div class="space-y-4">
                <div class="card-padded">
                    <div class="text-sm font-semibold text-gray-900">Announcements</div>
                    <div class="mt-1 text-sm text-gray-600">Latest updates from admin.</div>
                </div>

                <div class="space-y-3">
                    @forelse ($announcements as $announcement)
                        <div class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
                            <div class="text-sm font-semibold text-gray-900">{{ $announcement->title }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $announcement->published_at?->format('M j, Y g:i A') }}</div>
                            <div class="mt-3 text-sm text-gray-600">
                                {{ \Illuminate\Support\Str::limit($announcement->body, 140) }}
                            </div>
                            <div class="mt-4">
                                <a href="{{ route('announcements') }}" class="text-xs font-semibold text-brand-600 hover:text-brand-700">
                                    View all announcements
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-600">
                            No announcements yet.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
