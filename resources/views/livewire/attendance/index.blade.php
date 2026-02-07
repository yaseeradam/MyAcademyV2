<div class="space-y-6">
    <x-page-header title="Attendance" subtitle="Take and review daily attendance." accent="attendance">
        <x-slot:actions>
            <button type="button" wire:click="viewHistory" class="btn-outline">View History</button>
        </x-slot:actions>
    </x-page-header>

    <div class="card-padded">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                <select wire:model.live="classId" class="mt-2 select">
                    <option value="">Choose class</option>
                    @foreach ($this->classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Section</label>
                <select wire:model.live="sectionId" @disabled(! $classId) class="mt-2 select">
                    <option value="">Choose section</option>
                    @foreach ($this->sections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Term</label>
                <select wire:model.live="term" class="mt-2 select">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>

            <div class="lg:col-span-1">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Date</label>
                <input
                    wire:model.live="date"
                    type="date"
                    class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                />
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Session</label>
                <input
                    wire:model.live="session"
                    type="text"
                    placeholder="2025/2026"
                    class="mt-2 input-compact"
                />
            </div>

            <div class="lg:col-span-4">
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <button type="button" wire:click="start" class="btn-primary" @disabled(! $classId || ! $sectionId)>
                        Start Attendance
                    </button>
                    <button type="button" wire:click="save" class="btn-outline" @disabled(! $classId || ! $sectionId)>
                        Save
                    </button>

                    <div class="ml-auto flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="markAll('Present')" class="btn-ghost" @disabled(! $classId || ! $sectionId)>
                            Mark all Present
                        </button>
                        <button type="button" wire:click="markAll('Absent')" class="btn-ghost" @disabled(! $classId || ! $sectionId)>
                            Mark all Absent
                        </button>
                        <button type="button" wire:click="markAll('Late')" class="btn-ghost" @disabled(! $classId || ! $sectionId)>
                            Mark all Late
                        </button>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2 text-xs">
                    <x-status-badge variant="success">Present {{ $this->markCounts['Present'] ?? 0 }}</x-status-badge>
                    <x-status-badge variant="warning">Absent {{ $this->markCounts['Absent'] ?? 0 }}</x-status-badge>
                    <x-status-badge variant="info">Late {{ $this->markCounts['Late'] ?? 0 }}</x-status-badge>
                    <x-status-badge variant="default">Excused {{ $this->markCounts['Excused'] ?? 0 }}</x-status-badge>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex gap-6 border-b border-gray-100">
        <button
            type="button"
            wire:click="$set('tab', 'take')"
            class="{{ $tab === 'take' ? 'border-b-2 border-brand-500 text-brand-700' : 'border-b-2 border-transparent text-gray-600 hover:text-gray-900' }} -mb-px pb-3 text-sm font-semibold"
        >
            Take Attendance
        </button>
        <button
            type="button"
            wire:click="$set('tab', 'history')"
            class="{{ $tab === 'history' ? 'border-b-2 border-brand-500 text-brand-700' : 'border-b-2 border-transparent text-gray-600 hover:text-gray-900' }} -mb-px pb-3 text-sm font-semibold"
        >
            History
        </button>
    </div>

    @if ($tab === 'history')
        <div class="card-padded">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                    <select wire:model.live="historyClassId" class="mt-2 select">
                        <option value="">All classes</option>
                        @foreach ($this->historyClasses as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Section</label>
                    <select wire:model.live="historySectionId" @disabled(! $historyClassId) class="mt-2 select">
                        <option value="">All sections</option>
                        @foreach ($this->historySections as $section)
                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">From</label>
                    <input
                        wire:model.live="historyFrom"
                        type="date"
                        class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                    />
                </div>
                <div class="lg:col-span-1">
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">To</label>
                    <input
                        wire:model.live="historyTo"
                        type="date"
                        class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500"
                    />
                </div>
            </div>
        </div>

        <x-table>
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3">Class</th>
                    <th class="px-5 py-3">Session</th>
                    <th class="px-5 py-3 text-right">Present</th>
                    <th class="px-5 py-3 text-right">Absent</th>
                    <th class="px-5 py-3 text-right">Late</th>
                    <th class="px-5 py-3 text-right">Excused</th>
                    <th class="px-5 py-3">Taken By</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->historySheets as $sheet)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-5 py-4 text-sm text-gray-700">{{ $sheet->date?->format('M j, Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $sheet->schoolClass?->name }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $sheet->section?->name }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">
                            <div class="font-medium text-gray-900">{{ $sheet->session }}</div>
                            <div class="mt-1 text-xs text-gray-500">Term {{ $sheet->term }}</div>
                        </td>
                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">{{ number_format((int) $sheet->present_count) }}</td>
                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">{{ number_format((int) $sheet->absent_count) }}</td>
                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">{{ number_format((int) $sheet->late_count) }}</td>
                        <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">{{ number_format((int) $sheet->excused_count) }}</td>
                        <td class="px-5 py-4 text-sm text-gray-700">{{ $sheet->takenBy?->name ?: '-' }}</td>
                        <td class="px-5 py-4 text-right">
                            <button
                                type="button"
                                wire:click="openSheet({{ $sheet->id }})"
                                class="inline-flex items-center justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-brand-700 ring-1 ring-inset ring-brand-100 hover:bg-brand-50"
                            >
                                Open
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-10 text-center text-sm text-gray-500">No attendance history yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    @else
        <x-table>
            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Student</th>
                    <th class="px-5 py-3">Admission</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3">Note</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->students as $student)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $student->full_name }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-700">{{ $student->admission_number }}</td>
                        <td class="px-5 py-4">
                            <select
                                wire:model.lazy="marks.{{ $student->id }}.status"
                                class="select"
                            >
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Late">Late</option>
                                <option value="Excused">Excused</option>
                            </select>
                        </td>
                        <td class="px-5 py-4">
                            <input
                                wire:model.lazy="marks.{{ $student->id }}.note"
                                type="text"
                                placeholder="Optional note"
                                class="input-compact"
                            />
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-10 text-center text-sm text-gray-500">
                            Select a class and section to load students.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    @endif
</div>

