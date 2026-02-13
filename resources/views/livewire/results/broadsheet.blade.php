<div class="space-y-6">
    <x-page-header title="Broadsheet" subtitle="All students (rows) × all subjects (columns)." accent="results">
        <x-slot:actions>
            <a href="{{ route('results.entry') }}" class="btn-outline">Score Entry</a>
            @php($user = auth()->user())
            @if ($classId && $user?->hasPermission('results.publish'))
                <x-status-badge variant="{{ $this->isPublished ? 'success' : 'warning' }}">
                    {{ $this->isPublished ? 'Published' : 'Unpublished' }}
                </x-status-badge>
                @if ($this->isPublished)
                    <button type="button" wire:click="unpublishResults" class="btn-outline">Unpublish</button>
                @else
                    <button type="button" wire:click="publishResults" class="btn-primary">Publish</button>
                @endif
            @endif
        </x-slot:actions>
    </x-page-header>

    <div class="card-padded">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <div class="text-sm font-semibold text-slate-900">Filters</div>
                <div class="mt-1 text-sm text-slate-600">Pick class, session, and term.</div>
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
            </div>
        </div>
    </div>

    @if (! $classId)
        <div class="card-padded text-center">
            <div class="text-lg font-semibold text-gray-900">Select a class</div>
            <div class="mt-2 text-sm text-gray-600">Choose a class to generate the broadsheet.</div>
        </div>
    @elseif ($this->subjects->isEmpty())
        <div class="card-padded text-center">
            <div class="text-lg font-semibold text-gray-900">No subjects</div>
            <div class="mt-2 text-sm text-gray-600">Allocate subjects to this class to populate the broadsheet.</div>
        </div>
    @else
        <x-table class="text-xs">
            <thead class="bg-gray-50 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                <tr>
                    <th class="px-5 py-3">Student</th>
                    @foreach ($this->subjects as $subject)
                        <th class="px-4 py-3 text-right whitespace-nowrap">{{ $subject->code }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-right">Total</th>
                    <th class="px-4 py-3 text-right">Avg</th>
                    <th class="px-4 py-3 text-right">Pos</th>
                    <th class="px-5 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($this->rows as $row)
                    <tr class="bg-white hover:bg-gray-50">
                        <td class="px-5 py-4">
                            <div class="text-sm font-semibold text-gray-900">{{ $row['student']->full_name }}</div>
                            <div class="mt-1 text-xs text-gray-500">{{ $row['student']->admission_number }}</div>
                        </td>

                        @foreach ($this->subjects as $subject)
                            @php($val = $row['subjectTotals'][$subject->id] ?? null)
                            <td class="px-4 py-4 text-right text-sm font-semibold {{ $val === null ? 'text-gray-300' : 'text-gray-900' }}">
                                {{ $val ?? '—' }}
                            </td>
                        @endforeach

                        <td class="px-4 py-4 text-right text-sm font-bold text-gray-900">{{ $row['grandTotal'] }}</td>
                        <td class="px-4 py-4 text-right text-sm font-semibold text-gray-700">{{ number_format($row['average'], 2) }}</td>
                        <td class="px-4 py-4 text-right">
                            <x-status-badge variant="{{ $row['position'] <= 3 ? 'success' : 'neutral' }}">
                                {{ $row['position'] }}
                            </x-status-badge>
                        </td>
                        <td class="px-5 py-4 text-right">
                            @php($user = auth()->user())
                            @if ($this->isPublished || $user?->hasPermission('results.publish'))
                                <a
                                    href="{{ route('results.report-card', ['student' => $row['student'], 'term' => $term, 'session' => $session]) }}"
                                    class="text-sm font-semibold text-brand-600 hover:text-brand-700"
                                >
                                    Report Card
                                </a>
                            @else
                                <span class="text-xs font-semibold text-gray-400">Not published</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 1 + $this->subjects->count() + 4 }}" class="px-5 py-10 text-center text-sm text-gray-500">
                            No students found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    @endif
</div>
