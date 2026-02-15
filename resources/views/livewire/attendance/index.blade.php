<div class="space-y-6">
    <x-page-header title="Attendance" subtitle="Take daily attendance for your class" accent="attendance" />

    <div class="card-padded">
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                <select wire:model.live="classId" class="mt-2 select">
                    <option value="">Choose class</option>
                    @foreach ($this->classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Date</label>
                <input wire:model.live="date" type="date" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500" />
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
                <input wire:model.live="session" type="text" placeholder="2025/2026" class="mt-2 input-compact" />
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-3">
            <button type="button" wire:click="start" class="btn-primary" @disabled(! $classId)>
                Load Students
            </button>
            <button type="button" wire:click="save" class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-white text-green-700 border-2 border-green-200 hover:bg-green-50" @disabled(! $classId)>
                Save Attendance
            </button>

            <div class="ml-auto flex flex-wrap items-center gap-2">
                <button type="button" wire:click="markAll('Present')" class="rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-100" @disabled(! $classId)>
                    All Present
                </button>
                <button type="button" wire:click="markAll('Absent')" class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100" @disabled(! $classId)>
                    All Absent
                </button>
            </div>
        </div>

        <div class="mt-3 flex flex-wrap gap-2 text-xs">
            <x-status-badge variant="success">Present: {{ $this->markCounts['Present'] ?? 0 }}</x-status-badge>
            <x-status-badge variant="warning">Absent: {{ $this->markCounts['Absent'] ?? 0 }}</x-status-badge>
            <x-status-badge variant="info">Late: {{ $this->markCounts['Late'] ?? 0 }}</x-status-badge>
            <x-status-badge variant="default">Excused: {{ $this->markCounts['Excused'] ?? 0 }}</x-status-badge>
        </div>
    </div>

    <div class="space-y-3">
        @forelse ($this->students as $student)
            <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white p-3 shadow-sm">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-blue-50 text-sm font-bold text-blue-600 flex-shrink-0">
                    {{ mb_substr($student->full_name, 0, 1) }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="truncate text-sm font-semibold text-gray-900">{{ $student->full_name }}</div>
                    <div class="text-xs text-gray-500">{{ $student->admission_number }}</div>
                </div>
                @php
                    $status = $marks[$student->id]['status'] ?? 'Present';
                    $statusStyles = [
                        'Present' => 'bg-green-600 text-white',
                        'Absent' => 'bg-red-600 text-white',
                        'Late' => 'bg-orange-600 text-white',
                        'Excused' => 'bg-slate-600 text-white',
                    ];
                    $statusStyle = $statusStyles[$status] ?? $statusStyles['Present'];
                @endphp
                <button
                    type="button"
                    wire:click="cycleStatus({{ $student->id }})"
                    class="flex-shrink-0 rounded-lg px-4 py-2 text-xs font-semibold transition-colors {{ $statusStyle }}"
                    aria-label="Cycle attendance status for {{ $student->full_name }}"
                    title="Click to cycle status"
                >
                    {{ $status }}
                </button>
            </div>
        @empty
            <div class="card-padded text-center text-sm text-gray-500">
                Select a class and click "Load Students" to begin.
            </div>
        @endforelse
    </div>

</div>
