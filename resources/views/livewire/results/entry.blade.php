<div class="space-y-6">
    <x-page-header title="Score Entry" subtitle="Enter CA and Exam scores for a subject." accent="results">
        <x-slot:actions>
            <a href="{{ route('results.broadsheet') }}" class="btn-outline">Open Broadsheet</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card-padded">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-sm font-semibold text-slate-900">Filters</div>
                <div class="mt-1 text-sm text-slate-600">Pick class, subject, session, and term.</div>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                    <select
                        wire:model.live="classId"
                        class="mt-2 select min-w-52"
                    >
                        <option value="">Select class</option>
                        @foreach ($this->classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Subject</label>
                    <select
                        wire:model.live="subjectId"
                        @disabled(! $classId)
                        class="mt-2 select min-w-52"
                    >
                        <option value="">Select subject</option>
                        @foreach ($this->subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Session</label>
                    <input
                        wire:model.live.debounce.300ms="session"
                        type="text"
                        placeholder="2025/2026"
                        class="mt-2 input-compact min-w-40"
                    />
                </div>

                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Term</label>
                    <select
                        wire:model.live="term"
                        class="mt-2 select min-w-24"
                    >
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                    </select>
                </div>

                <button
                    type="button"
                    wire:click="save"
                    @disabled(! $classId || ! $subjectId)
                    class="btn-primary"
                >
                    Save Scores
                </button>
            </div>
        </div>
    </div>

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
        <x-table>
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Student</th>
                    <th class="px-5 py-3 text-right">CA1 /20</th>
                    <th class="px-5 py-3 text-right">CA2 /20</th>
                    <th class="px-5 py-3 text-right">Exam /60</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3 text-right">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->students as $student)
                    @php
                        $row = $scores[$student->id] ?? ['ca1' => 0, 'ca2' => 0, 'exam' => 0];
                        $total = (int) ($row['ca1'] ?? 0) + (int) ($row['ca2'] ?? 0) + (int) ($row['exam'] ?? 0);
                        $grade = \App\Models\Score::gradeForTotal($total);
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
                                max="20"
                                class="w-20 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                        </td>
                        <td class="px-5 py-4 text-right">
                            <input
                                wire:model.lazy="scores.{{ $student->id }}.ca2"
                                type="number"
                                min="0"
                                max="20"
                                class="w-20 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                            />
                        </td>
                        <td class="px-5 py-4 text-right">
                            <input
                                wire:model.lazy="scores.{{ $student->id }}.exam"
                                type="number"
                                min="0"
                                max="60"
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
