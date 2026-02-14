@php
    $isAdmin = $me?->role === 'admin';
@endphp

<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 via-pink-500 to-fuchsia-600 p-8 shadow-xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxwYXRoIGQ9Ik0zNiAxOGMzLjMxNCAwIDYgMi42ODYgNiA2cy0yLjY4NiA2LTYgNi02LTIuNjg2LTYtNiAyLjY4Ni02IDYtNiIgc3Ryb2tlPSIjZmZmIiBzdHJva2Utd2lkdGg9IjIiIG9wYWNpdHk9Ii4xIi8+PC9nPjwvc3ZnPg==')] opacity-30"></div>
        <div class="relative">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">CBT Exam Monitor</h1>
                    <p class="mt-2 text-base text-pink-50">Teachers set questions → submit → admin approves → students take exam</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    @if ($me?->role === 'teacher')
                        <button type="button" wire:click="{{ $creating ? 'cancelCreate' : 'startCreate' }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-rose-600 shadow-lg transition-all hover:bg-pink-50 hover:shadow-xl">
                            {{ $creating ? 'Close' : 'New Exam' }}
                        </button>
                    @endif
                    @if ($me?->role === 'admin')
                        <button type="button" wire:click="{{ $requesting ? 'cancelRequest' : 'startRequest' }}" class="rounded-xl bg-white px-5 py-2.5 text-sm font-semibold text-rose-600 shadow-lg transition-all hover:bg-pink-50 hover:shadow-xl">
                            {{ $requesting ? 'Close' : 'Request Teacher' }}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-lg">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-sm font-semibold text-gray-900">Exams</div>
                <div class="mt-1 text-sm text-gray-600">Manage your CBT exams and approvals.</div>
            </div>

            <div class="flex items-center gap-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                <select wire:model.live="statusFilter" class="select min-w-48">
                    <option value="">All</option>
                    <option value="assigned">Assigned</option>
                    <option value="draft">Draft</option>
                    <option value="submitted">Submitted</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    @if ($requesting)
        <div class="rounded-2xl border border-pink-200 bg-gradient-to-br from-pink-50 to-fuchsia-50 p-6 shadow-lg">
            <div class="text-sm font-semibold text-gray-900">Request Teacher</div>
            <div class="mt-1 text-sm text-gray-600">Send a CBT question request to a teacher allocated to the class and subject.</div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Title</label>
                    <input wire:model="title" class="mt-2 input w-full" placeholder="e.g. Mathematics CBT - Test 1" />
                    @error('title') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Duration (minutes)</label>
                    <input wire:model="durationMinutes" type="number" min="1" max="300" class="mt-2 input w-full" />
                    @error('durationMinutes') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                    <select wire:model.live="classId" class="mt-2 select w-full">
                        <option value="">Select class</option>
                        @foreach ($this->classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('classId') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Subject</label>
                    <select wire:model.live="subjectId" @disabled(! $classId) class="mt-2 select w-full">
                        <option value="">Select subject</option>
                        @foreach ($this->subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subjectId') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Teacher</label>
                    <select wire:model.live="teacherId" @disabled(! $classId || ! $subjectId) class="mt-2 select w-full">
                        <option value="">Select teacher</option>
                        @foreach ($this->teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('teacherId') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Session</label>
                    <input wire:model="session" class="mt-2 input w-full" placeholder="2025/2026" />
                    @error('session') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Term</label>
                    <select wire:model.live="term" class="mt-2 select w-full">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    @error('term') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Request Note (optional)</label>
                    <input wire:model="requestNote" class="mt-2 input w-full" placeholder="e.g. Focus on topics 1-5." />
                    @error('requestNote') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Description (optional)</label>
                    <input wire:model="description" class="mt-2 input w-full" placeholder="Instructions for students..." />
                    @error('description') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <button type="button" wire:click="createRequest" class="btn-primary">Send Request</button>
                <button type="button" wire:click="cancelRequest" class="btn-outline">Cancel</button>
            </div>
        </div>
    @endif

    @if ($creating)
        <div class="rounded-2xl border border-pink-200 bg-gradient-to-br from-pink-50 to-fuchsia-50 p-6 shadow-lg">
            <div class="text-sm font-semibold text-gray-900">Create Exam</div>
            <div class="mt-1 text-sm text-gray-600">Draft an exam first, then add questions.</div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Title</label>
                    <input wire:model="title" class="mt-2 input w-full" placeholder="e.g. Mathematics Quiz 1" />
                    @error('title') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Duration (minutes)</label>
                    <input wire:model="durationMinutes" type="number" min="1" max="300" class="mt-2 input w-full" />
                    @error('durationMinutes') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                    <select wire:model.live="classId" class="mt-2 select w-full">
                        <option value="">Select class</option>
                        @foreach ($this->classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('classId') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Subject</label>
                    <select wire:model.live="subjectId" @disabled(! $classId) class="mt-2 select w-full">
                        <option value="">Select subject</option>
                        @foreach ($this->subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subjectId') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Term</label>
                    <select wire:model.live="term" class="mt-2 select w-full">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                    @error('term') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Session</label>
                <input wire:model="session" class="mt-2 input w-full" placeholder="2025/2026" />
                @error('session') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Description (optional)</label>
                <textarea wire:model="description" rows="3" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Instructions for students..."></textarea>
                @error('description') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                <button type="button" wire:click="createExam" class="btn-primary">Create</button>
                <button type="button" wire:click="cancelCreate" class="btn-outline">Cancel</button>
            </div>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl bg-white shadow-lg">
        <x-table>
            <thead class="bg-gradient-to-r from-rose-500 to-fuchsia-600 text-xs font-semibold uppercase tracking-wider text-white">
                <tr>
                    <th class="px-5 py-3">Exam</th>
                    <th class="px-5 py-3">Class / Subject</th>
                    <th class="px-5 py-3 text-center">Questions</th>
                    <th class="px-5 py-3 text-center">Attempts</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Code</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($exams as $exam)
                    @php
                        $variant = match ($exam->status) {
                            'approved' => 'success',
                            'submitted' => 'info',
                            'assigned' => 'info',
                            'rejected' => 'warning',
                            default => 'neutral',
                        };
                    @endphp
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $exam->title }}</div>
                            <div class="mt-1 text-xs text-gray-500">
                                @if ($isAdmin)
                                    @if ($exam->status === 'assigned')
                                        Teacher: {{ $exam->assignedTeacher?->name ?? 'Not set' }}
                                    @else
                                        By {{ $exam->creator?->name ?? 'User' }}
                                    @endif
                                @else
                                    {{ $exam->session ? $exam->session.' ' : '' }}T{{ $exam->term }}
                                @endif
                            </div>
                            @if ($exam->status === 'assigned' && $exam->request_note)
                                <div class="mt-2 text-xs text-slate-700">Request: {{ $exam->request_note }}</div>
                            @endif
                            @if ($exam->status === 'rejected' && $exam->note)
                                <div class="mt-2 text-xs text-orange-700">Note: {{ $exam->note }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $exam->schoolClass?->name ?? '-' }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $exam->subject?->name ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4 text-center text-sm font-semibold text-gray-900">{{ (int) $exam->questions_count }}</td>
                        <td class="px-5 py-4 text-center text-sm font-semibold text-gray-900">{{ (int) $exam->attempts_count }}</td>
                        <td class="px-5 py-4">
                            <x-status-badge variant="{{ $variant }}">{{ ucfirst($exam->status) }}</x-status-badge>
                        </td>
                        <td class="px-5 py-4">
                            <span class="text-sm font-mono text-gray-700">{{ $exam->status === 'approved' ? ($exam->access_code ?: '-') : '-' }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('cbt.exams.edit', $exam) }}" class="btn-outline">
                                {{ in_array($exam->status, ['draft', 'assigned', 'rejected'], true) ? 'Edit' : 'Open' }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-sm text-gray-600">No exams yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
</div>
