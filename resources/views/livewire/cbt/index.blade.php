@php
    $isAdmin = $me?->role === 'admin';
@endphp

<div class="space-y-6">
    <div class="rounded-2xl bg-gradient-to-r from-rose-500 to-fuchsia-600 p-6 shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">CBT Exams</h1>
                <p class="mt-1 text-sm text-pink-50">Create → Submit → Approve → Go Live</p>
            </div>
            <div class="flex gap-2">
                @if ($me?->role === 'teacher')
                    <button wire:click="{{ $creating ? 'cancelCreate' : 'startCreate' }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-pink-50">{{ $creating ? 'Close' : 'New Exam' }}</button>
                @endif
                @if ($me?->role === 'admin')
                    <button wire:click="{{ $requesting ? 'cancelRequest' : 'startRequest' }}" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-rose-600 hover:bg-pink-50">{{ $requesting ? 'Close' : 'Request Teacher' }}</button>
                @endif
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-4 shadow-lg">
        <div class="flex items-center justify-between">
            <div class="text-sm font-semibold text-gray-900">{{ $exams->count() }} Exams</div>
            <select wire:model.live="statusFilter" class="select w-40">
                <option value="">All Status</option>
                <option value="assigned">Assigned</option>
                <option value="draft">Draft</option>
                <option value="submitted">Submitted</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
        </div>
    </div>

    @if ($requesting)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 shadow-lg">
            <div class="text-sm font-semibold text-gray-900">Request Teacher</div>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-500">Title</label>
                    <input wire:model="title" class="mt-1 input w-full" placeholder="Mathematics CBT - Test 1" />
                    @error('title') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Class</label>
                    <select wire:model.live="classId" class="mt-1 select w-full">
                        <option value="">Select</option>
                        @foreach ($this->classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('classId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Subject</label>
                    <select wire:model.live="subjectId" @disabled(! $classId) class="mt-1 select w-full">
                        <option value="">Select</option>
                        @foreach ($this->subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subjectId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Teacher</label>
                    <select wire:model.live="teacherId" @disabled(! $classId || ! $subjectId) class="mt-1 select w-full">
                        <option value="">Select</option>
                        @foreach ($this->teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                    @error('teacherId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Duration (min)</label>
                    <input wire:model="durationMinutes" type="number" min="1" class="mt-1 input w-full" />
                    @error('durationMinutes') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Term</label>
                    <select wire:model.live="term" class="mt-1 select w-full">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Session</label>
                    <input wire:model="session" class="mt-1 input w-full" placeholder="2025/2026" />
                    @error('session') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-500">Request Note</label>
                    <input wire:model="requestNote" class="mt-1 input w-full" placeholder="Focus on topics 1-5" />
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button wire:click="createRequest" class="btn-primary">Send Request</button>
                <button wire:click="cancelRequest" class="btn-outline">Cancel</button>
            </div>
        </div>
    @endif

    @if ($creating)
        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 shadow-lg">
            <div class="text-sm font-semibold text-gray-900">Create Exam</div>
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-500">Title</label>
                    <input wire:model="title" class="mt-1 input w-full" placeholder="Mathematics Quiz 1" />
                    @error('title') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Class</label>
                    <select wire:model.live="classId" class="mt-1 select w-full">
                        <option value="">Select</option>
                        @foreach ($this->classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('classId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Subject</label>
                    <select wire:model.live="subjectId" @disabled(! $classId) class="mt-1 select w-full">
                        <option value="">Select</option>
                        @foreach ($this->subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subjectId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Duration (min)</label>
                    <input wire:model="durationMinutes" type="number" min="1" class="mt-1 input w-full" />
                    @error('durationMinutes') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase text-gray-500">Term</label>
                    <select wire:model.live="term" class="mt-1 select w-full">
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase text-gray-500">Session</label>
                    <input wire:model="session" class="mt-1 input w-full" placeholder="2025/2026" />
                    @error('session') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-4 flex gap-2">
                <button wire:click="createExam" class="btn-primary">Create</button>
                <button wire:click="cancelCreate" class="btn-outline">Cancel</button>
            </div>
        </div>
    @endif

    <div class="grid gap-4 lg:grid-cols-2">
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
            <div class="rounded-2xl border bg-white p-4 shadow-lg">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-gray-900">{{ $exam->title }}</div>
                        <div class="mt-1 text-xs text-gray-500">{{ $exam->schoolClass?->name }} • {{ $exam->subject?->name }}</div>
                        @if ($exam->status === 'assigned' && $exam->request_note)
                            <div class="mt-2 rounded bg-indigo-50 px-2 py-1 text-xs text-indigo-700">{{ $exam->request_note }}</div>
                        @endif
                        @if ($exam->status === 'rejected' && $exam->note)
                            <div class="mt-2 rounded bg-orange-50 px-2 py-1 text-xs text-orange-700">{{ $exam->note }}</div>
                        @endif
                    </div>
                    <x-status-badge variant="{{ $variant }}">{{ ucfirst($exam->status) }}</x-status-badge>
                </div>
                
                <div class="mt-3 flex items-center gap-4 text-xs text-gray-600">
                    <div><span class="font-semibold">{{ (int) $exam->questions_count }}</span> Questions</div>
                    <div><span class="font-semibold">{{ (int) $exam->attempts_count }}</span> Attempts</div>
                    @if ($exam->status === 'approved' && $exam->access_code)
                        <div class="font-mono font-semibold text-rose-600">{{ $exam->access_code }}</div>
                    @endif
                </div>
                
                <div class="mt-3 flex items-center justify-between border-t pt-3">
                    <div class="text-xs text-gray-500">
                        @if ($isAdmin && $exam->status === 'assigned')
                            {{ $exam->assignedTeacher?->name ?? 'No teacher' }}
                        @else
                            {{ $exam->creator?->name ?? 'User' }}
                        @endif
                    </div>
                    <a href="{{ route('cbt.exams.edit', $exam) }}" class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-semibold hover:bg-gray-200">
                        {{ in_array($exam->status, ['draft', 'assigned', 'rejected'], true) ? 'Edit' : 'Open' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="lg:col-span-2 rounded-2xl border-2 border-dashed bg-gray-50 p-10 text-center text-sm text-gray-600">No exams yet</div>
        @endforelse
    </div>


</div>
