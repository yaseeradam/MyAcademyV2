<div class="space-y-6">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-sky-500 via-blue-500 to-cyan-600 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PHBhdHRlcm4gaWQ9ImdyaWQiIHdpZHRoPSI2MCIgaGVpZ2h0PSI2MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+PHBhdGggZD0iTSAxMCAwIEwgMCAwIDAgMTAiIGZpbGw9Im5vbmUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS1vcGFjaXR5PSIwLjEiIHN0cm9rZS13aWR0aD0iMSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmlkKSIvPjwvc3ZnPg==')] opacity-30"></div>
        <div class="relative flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-white">Timetable</h1>
                <p class="mt-2 text-sky-100">Class scheduling and weekly timetable management</p>
            </div>
            <a href="{{ route('more-features') }}" class="rounded-xl bg-white/20 px-4 py-2.5 text-sm font-semibold text-white backdrop-blur-sm transition-all hover:bg-white/30">
                Back
            </a>
        </div>
    </div>

    <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-white to-sky-50/30 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-sm font-bold text-sky-900">Filters</div>
                <div class="mt-1 text-sm text-sky-700">Pick class/section/day to view the timetable</div>
            </div>

            <div class="grid w-full gap-3 sm:grid-cols-3 lg:max-w-4xl">
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Class</label>
                    <select wire:model.live="classId" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="">All</option>
                        @foreach($this->classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Section</label>
                    <select wire:model.live="sectionId" @disabled(! $classId) class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 disabled:bg-gray-50">
                        <option value="">All</option>
                        @foreach($this->sections as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Day</label>
                    <select wire:model.live="day" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="">All</option>
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                        <option value="7">Sunday</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($isAdmin)
        <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-white to-sky-50/30 p-6 shadow-lg backdrop-blur-sm" id="timetable-form">
            <div class="flex items-center gap-3">
                <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-lg">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-bold text-sky-900">Add / Edit Entry</div>
                    <div class="text-sm text-sky-700">Avoid overlaps for a clean timetable</div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Class</label>
                    <select wire:model.live="entryClassId" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="">Select</option>
                        @foreach($this->classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('entryClassId') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Section</label>
                    <select wire:model.live="entrySectionId" @disabled(! $entryClassId) class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 disabled:bg-gray-50">
                        <option value="">All</option>
                        @foreach($this->entrySections as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('entrySectionId') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Day</label>
                    <select wire:model.live="entryDay" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                        <option value="7">Sunday</option>
                    </select>
                    @error('entryDay') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Room</label>
                    <input wire:model="room" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500" placeholder="e.g. Lab 1" />
                    @error('room') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Starts</label>
                    <input wire:model="startsAt" type="time" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500" />
                    @error('startsAt') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Ends</label>
                    <input wire:model="endsAt" type="time" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500" />
                    @error('endsAt') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Subject</label>
                    <select wire:model.live="subjectId" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="">Select</option>
                        @foreach($this->subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('subjectId') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-sky-700">Teacher</label>
                    <select wire:model.live="teacherId" class="mt-2 w-full rounded-lg border border-sky-200 bg-white px-4 py-2.5 text-sm text-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="">(none)</option>
                        @foreach($this->teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    @error('teacherId') <div class="mt-1 text-xs text-rose-600">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" wire:click="clearForm" class="rounded-xl border border-sky-200 bg-white px-5 py-2.5 text-sm font-semibold text-sky-700 shadow-sm hover:bg-sky-50">
                    Clear
                </button>
                <button type="button" wire:click="save" class="rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:from-sky-600 hover:to-blue-700">
                    {{ $editingId ? 'Update Entry' : 'Save Entry' }}
                </button>
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-white to-sky-50/30 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 10h18M3 14h18m-9-4v8m-7 2h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-sky-900">Timetable Grid</div>
                <div class="text-sm text-sky-700">
                    @if(! $canUseGrid)
                        Select a class to use the timetable grid
                    @elseif(! $isAdmin)
                        Viewing the timetable grid for this class
                    @elseif($gridUsesSections)
                        Click a cell to add a subject, or click a filled cell to edit
                    @else
                        Showing class-wide slots only. Select a section to add section-specific entries
                    @endif
                </div>
            </div>
        </div>

        @if($canUseGrid)
            <div class="mt-6 overflow-x-auto rounded-xl border border-sky-200 bg-white shadow-sm">
                <table class="min-w-full">
                    <thead class="bg-gradient-to-r from-sky-500 to-blue-600">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">Time</th>
                        @foreach($days as $d)
                            <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wider text-white">{{ $d['label'] }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-sky-100">
                    @foreach($timeSlots as $slot)
                        <tr>
                            <td class="px-4 py-3 text-xs font-bold text-sky-700">
                                {{ $slot['label'] }}
                            </td>
                            @foreach($days as $d)
                                @php
                                    $entry = $slotMap[$d['day']][$slot['key']] ?? null;
                                @endphp
                                <td class="px-3 py-3 align-top">
                    @if($entry)
                        @if($isAdmin)
                            <button type="button" wire:click="edit({{ $entry->id }})" onclick="document.getElementById('timetable-form')?.scrollIntoView({behavior: 'smooth', block: 'start'});" class="w-full rounded-lg border border-sky-200 bg-gradient-to-br from-sky-50 to-blue-50 px-3 py-2.5 text-left text-xs shadow-sm hover:border-sky-300 hover:shadow-md">
                                <div class="font-bold text-sky-900">{{ $entry->subject?->name ?? 'Subject' }}</div>
                                <div class="mt-1 text-xs text-sky-700">
                                    {{ $entry->teacher?->name ?? 'No teacher' }}
                                    @if($entry->room)
                                        · {{ $entry->room }}
                                    @endif
                                </div>
                            </button>
                        @else
                            <div class="rounded-lg border border-sky-200 bg-gradient-to-br from-sky-50 to-blue-50 px-3 py-2.5 text-xs shadow-sm">
                                <div class="font-bold text-sky-900">{{ $entry->subject?->name ?? 'Subject' }}</div>
                                <div class="mt-1 text-xs text-sky-700">
                                    {{ $entry->teacher?->name ?? 'No teacher' }}
                                    @if($entry->room)
                                        · {{ $entry->room }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    @else
                        @if($isAdmin)
                            <button type="button" wire:click="selectSlot({{ $d['day'] }}, @js($slot['start']), @js($slot['end']))" onclick="document.getElementById('timetable-form')?.scrollIntoView({behavior: 'smooth', block: 'start'});" class="w-full rounded-lg border border-dashed border-sky-200 bg-white px-2 py-2 text-left text-xs text-sky-500 hover:border-sky-300 hover:bg-sky-50">
                                Add
                            </button>
                        @else
                            <div class="text-xs text-gray-400">-</div>
                        @endif
                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="mt-6 rounded-xl border border-dashed border-sky-200 bg-sky-50/50 p-6 text-center text-sm text-sky-700">
                Pick a class from the filters to enable the grid
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-sky-100 bg-gradient-to-br from-white to-sky-50/30 p-6 shadow-lg backdrop-blur-sm">
        <div class="flex items-center gap-3">
            <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br from-sky-500 to-blue-600 text-white shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div>
                <div class="text-sm font-bold text-sky-900">Schedule Details</div>
                <div class="text-sm text-sky-700">Grouped by day</div>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            @forelse($grouped as $g)
                <div class="rounded-xl border border-sky-200 bg-white p-4 shadow-sm">
                    <div class="text-sm font-bold text-sky-900">{{ $g['label'] }}</div>
                    <div class="mt-3 overflow-x-auto">
                        <table class="min-w-full divide-y divide-sky-100">
                            <thead class="bg-sky-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider text-sky-700">Time</th>
                                <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider text-sky-700">Class</th>
                                <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider text-sky-700">Section</th>
                                <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider text-sky-700">Subject</th>
                                <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider text-sky-700">Teacher</th>
                                <th class="px-4 py-2 text-left text-xs font-bold uppercase tracking-wider text-sky-700">Room</th>
                                @if($isAdmin)
                                    <th class="px-4 py-2 text-right text-xs font-bold uppercase tracking-wider text-sky-700">Actions</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-100">
                            @foreach($g['rows'] as $row)
                                <tr class="hover:bg-sky-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ substr((string) $row->starts_at, 0, 5) }} - {{ substr((string) $row->ends_at, 0, 5) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row->schoolClass?->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row->section?->name ?? 'All' }}</td>
                                    <td class="px-4 py-3 text-sm font-bold text-sky-900">{{ $row->subject?->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row->teacher?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row->room ?? '—' }}</td>
                                    @if($isAdmin)
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" wire:click="edit({{ $row->id }})" class="rounded-lg border border-sky-200 bg-white px-3 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-50">Edit</button>
                                                <button type="button" wire:click="delete({{ $row->id }})" onclick="return confirm('Delete entry?')" class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-rose-700">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-sky-200 bg-sky-50/50 p-8 text-center text-sm text-sky-700">
                    No timetable entries yet
                </div>
            @endforelse
        </div>
    </div>
</div>
