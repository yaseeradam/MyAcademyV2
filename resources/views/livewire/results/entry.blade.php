@php
    $user = auth()->user();
    $submissionStatus = $this->submission?->status;
    $locked = $user?->role === 'teacher' && (in_array($submissionStatus, ['submitted', 'approved'], true) || $this->isPublished);
@endphp

<div class="space-y-6">
    <x-page-header title="Score Entry" subtitle="Enter CA and Exam scores for students" accent="results">
        <x-slot:actions>
            <a href="{{ route('results.broadsheet') }}" class="btn-outline">Broadsheet</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card-padded">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                <select wire:model.live="classId" class="mt-2 select">
                    <option value="">Select class</option>
                    @foreach ($this->classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Subject</label>
                <select wire:model.live="subjectId" @disabled(! $classId) class="mt-2 select">
                    <option value="">Select subject</option>
                    @foreach ($this->subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Term</label>
                <select wire:model.live="term" class="mt-2 select">
                    <option value="1">Term 1</option>
                    <option value="2">Term 2</option>
                    <option value="3">Term 3</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Session</label>
                <input wire:model.live.debounce.300ms="session" type="text" placeholder="2025/2026" class="mt-2 input-compact" />
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="button" wire:click="save" @disabled(! $classId || ! $subjectId || $locked) class="btn-primary">
                Save Scores
            </button>

            @if ($user?->role === 'teacher')
                <button type="button" wire:click="submitScores" @disabled(! $classId || ! $subjectId || $locked) class="btn-outline">
                    Submit to Admin
                </button>
            @endif

            @if ($this->isPublished)
                <x-status-badge variant="success">Published</x-status-badge>
            @endif

            @if ($user?->role === 'teacher' && $this->submission)
                @php
                    $status = $this->submission->status ?? 'submitted';
                    $variant = match ($status) {
                        'approved' => 'success',
                        'rejected' => 'warning',
                        'submitted' => 'info',
                        default => 'neutral',
                    };
                @endphp
                <x-status-badge variant="{{ $variant }}">{{ ucfirst($status) }}</x-status-badge>
                @if ($status === 'rejected' && $this->submission->note)
                    <span class="text-xs text-orange-700">Note: {{ $this->submission->note }}</span>
                @endif
            @endif
        </div>
    </div>

    @if ($user?->role === 'admin')
        <div class="card-padded">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Score Submissions</div>
                    <div class="mt-1 text-sm text-gray-600">Review teacher submissions and approve or reject.</div>
                </div>
            </div>

            <div class="mt-4 space-y-4">
                @forelse ($this->submissions as $submission)
                    <div wire:key="score-submission-{{ $submission->id }}" class="rounded-2xl border border-gray-100 bg-white p-4">
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $submission->teacher?->name ?? 'Teacher' }}
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    {{ $submission->schoolClass?->name ?? '-' }}
                                    / {{ $submission->subject?->name ?? '-' }}
                                    / {{ $submission->session }} T{{ $submission->term }}
                                </div>
                                @if ($submission->note)
                                    <div class="mt-2 text-xs text-orange-700">Note: {{ $submission->note }}</div>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-center gap-2">
                                @php
                                    $status = $submission->status ?? 'submitted';
                                    $variant = match ($status) {
                                        'approved' => 'success',
                                        'rejected' => 'warning',
                                        'submitted' => 'info',
                                        default => 'neutral',
                                    };
                                    $canReview = $status === 'submitted';
                                @endphp
                                <x-status-badge variant="{{ $variant }}">{{ ucfirst($status) }}</x-status-badge>
                                <a
                                    href="{{ route('results.entry', ['class' => $submission->class_id, 'subject' => $submission->subject_id, 'term' => $submission->term, 'session' => $submission->session]) }}"
                                    class="btn-outline"
                                >
                                    Open
                                </a>
                                <button
                                    type="button"
                                    wire:click="approveSubmission({{ $submission->id }})"
                                    @disabled(! $canReview)
                                    wire:loading.attr="disabled"
                                    wire:target="approveSubmission,confirmReject"
                                    class="btn-primary"
                                >
                                    Approve
                                </button>
                                <button
                                    type="button"
                                    wire:click="startReject({{ $submission->id }})"
                                    @disabled(! $canReview)
                                    wire:loading.attr="disabled"
                                    wire:target="startReject,confirmReject,approveSubmission"
                                    class="btn-outline"
                                >
                                    Reject
                                </button>
                            </div>
                        </div>

                        @if ($rejectingId === $submission->id)
                            <div class="mt-4 rounded-2xl border border-orange-100 bg-orange-50/60 p-4">
                                <label class="text-xs font-semibold uppercase tracking-wider text-orange-700">Rejection note</label>
                                <textarea
                                    wire:model="rejectNote"
                                    rows="3"
                                    class="mt-2 w-full rounded-xl border border-orange-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-orange-400 focus:ring-orange-300"
                                    placeholder="Explain what needs to be corrected..."
                                ></textarea>
                                @error('rejectNote') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                                <div class="mt-3 flex items-center justify-end gap-2">
                                    <button type="button" wire:click="cancelReject" class="btn-outline">Cancel</button>
                                    <button type="button" wire:click="confirmReject" wire:loading.attr="disabled" wire:target="confirmReject" class="btn-primary">Confirm Reject</button>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-600">
                        No submissions yet.
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    @if (! $classId)
        <div class="card-padded text-center">
            <div class="text-lg font-semibold text-gray-900">Select a class</div>
            <div class="mt-2 text-sm text-gray-600">Choose a class to load students and subjects.</div>
        </div>
	    @elseif (! $subjectId)
	        <div class="card-padded text-center">
	            <div class="text-lg font-semibold text-gray-900">Select a subject</div>
	            <div class="mt-2 text-sm text-gray-600">Only allocated subjects are shown for teachers.</div>
	        </div>
	    @else
	        @php
	            $maxMarks = $this->maxMarks();
	        @endphp
	        <x-table>
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Student</th>
                    <th class="px-5 py-3 text-right">CA1 /{{ $maxMarks['ca1'] }}</th>
                    <th class="px-5 py-3 text-right">CA2 /{{ $maxMarks['ca2'] }}</th>
                    <th class="px-5 py-3 text-right">Exam /{{ $maxMarks['exam'] }}</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3 text-right">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->students as $student)
                    @php
                        $row = $scores[$student->id] ?? ['ca1' => 0, 'ca2' => 0, 'exam' => 0];
                        $total = (int) ($row['ca1'] ?? 0) + (int) ($row['ca2'] ?? 0) + (int) ($row['exam'] ?? 0);
                        $grade = \App\Models\Score::gradeForTotal($total, $maxMarks['ca1'] + $maxMarks['ca2'] + $maxMarks['exam']);
                    @endphp
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $student->full_name }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $student->admission_number }}</div>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <input
                                wire:model.lazy="scores.{{ $student->id }}.ca1"
                                type="number"
                                min="0"
                                max="{{ $maxMarks['ca1'] }}"
                                step="1"
                                oninput="this.value !== '' && (this.value = Math.max(+this.min, Math.min(+this.max, +this.value)))"
                                class="w-20 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                        </td>
                        <td class="px-5 py-4 text-right">
                            <input
                                wire:model.lazy="scores.{{ $student->id }}.ca2"
                                type="number"
                                min="0"
                                max="{{ $maxMarks['ca2'] }}"
                                step="1"
                                oninput="this.value !== '' && (this.value = Math.max(+this.min, Math.min(+this.max, +this.value)))"
                                class="w-20 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                        </td>
                        <td class="px-5 py-4 text-right">
                            <input
                                wire:model.lazy="scores.{{ $student->id }}.exam"
                                type="number"
                                min="0"
                                max="{{ $maxMarks['exam'] }}"
                                step="1"
                                oninput="this.value !== '' && (this.value = Math.max(+this.min, Math.min(+this.max, +this.value)))"
                                class="w-20 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                        </td>
                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">{{ $total }}</td>
                        <td class="px-5 py-4 text-right">
                            <x-status-badge variant="{{ in_array($grade, ['A', 'B'], true) ? 'success' : 'neutral' }}">
                                {{ $grade }}
                            </x-status-badge>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">No students in this class.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    @endif
</div>
