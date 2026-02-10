<div class="space-y-6">
    <x-page-header title="Timetable" subtitle="Class scheduling and weekly timetable." accent="more">
        <x-slot:actions>
            <a href="{{ route('more-features') }}" class="btn-outline">Back</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card-padded">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="text-sm font-semibold text-slate-900">Filters</div>
                <div class="mt-1 text-sm text-slate-600">Pick class/section/day to view the timetable.</div>
            </div>

            <div class="grid w-full gap-3 sm:grid-cols-3 lg:max-w-4xl">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                    <select wire:model.live="classId" class="mt-2 select w-full">
                        <option value="">All</option>
                        @foreach($this->classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Section</label>
                    <select wire:model.live="sectionId" @disabled(! $classId) class="mt-2 select w-full">
                        <option value="">All</option>
                        @foreach($this->sections as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Day</label>
                    <select wire:model.live="day" class="mt-2 select w-full">
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
        <div class="card-padded" id="timetable-form">
            <div class="text-sm font-semibold text-gray-900">Add / Edit Entry</div>
            <div class="mt-1 text-sm text-gray-600">Avoid overlaps for a clean timetable.</div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Class</label>
                    <select wire:model.live="entryClassId" class="mt-2 select w-full">
                        <option value="">Select</option>
                        @foreach($this->classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('entryClassId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Section</label>
                    <select wire:model.live="entrySectionId" @disabled(! $entryClassId) class="mt-2 select w-full">
                        <option value="">All</option>
                        @foreach($this->entrySections as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('entrySectionId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Day</label>
                    <select wire:model.live="entryDay" class="mt-2 select w-full">
                        <option value="1">Monday</option>
                        <option value="2">Tuesday</option>
                        <option value="3">Wednesday</option>
                        <option value="4">Thursday</option>
                        <option value="5">Friday</option>
                        <option value="6">Saturday</option>
                        <option value="7">Sunday</option>
                    </select>
                    @error('entryDay') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Room</label>
                    <input wire:model="room" class="mt-2 input w-full" placeholder="e.g. Lab 1" />
                    @error('room') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Starts</label>
                    <input wire:model="startsAt" type="time" class="mt-2 input w-full" />
                    @error('startsAt') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ends</label>
                    <input wire:model="endsAt" type="time" class="mt-2 input w-full" />
                    @error('endsAt') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Subject</label>
                    <select wire:model.live="subjectId" class="mt-2 select w-full">
                        <option value="">Select</option>
                        @foreach($this->subjects as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('subjectId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Teacher</label>
                    <select wire:model.live="teacherId" class="mt-2 select w-full">
                        <option value="">(none)</option>
                        @foreach($this->teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>
                    @error('teacherId') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mt-4 flex items-center justify-end gap-2">
                <button type="button" wire:click="save" class="btn-primary">
                    {{ $editingId ? 'Update' : 'Save' }}
                </button>
                <button type="button" wire:click="clearForm" class="btn-outline">Clear</button>
            </div>
        </div>
    @endif

    <div class="card-padded">
        <div class="text-sm font-semibold text-gray-900">Timetable Grid</div>
        <div class="mt-1 text-sm text-gray-600">
            @if(! $canUseGrid)
                Select a class to use the timetable grid.
            @elseif(! $isAdmin)
                Viewing the timetable grid for this class.
            @elseif($gridUsesSections)
                Click a cell to add a subject, or click a filled cell to edit.
            @else
                Showing class-wide slots only. Select a section to add section-specific entries.
            @endif
        </div>

        @if($canUseGrid)
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full border-separate border-spacing-0">
                    <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-4 py-2 text-left">Time</th>
                        @foreach($days as $d)
                            <th class="px-4 py-2 text-left">{{ $d['label'] }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @foreach($timeSlots as $slot)
                        <tr>
                            <td class="px-4 py-3 text-xs font-semibold text-gray-600">
                                {{ $slot['label'] }}
                            </td>
                            @foreach($days as $d)
                                @php
                                    $entry = $slotMap[$d['day']][$slot['key']] ?? null;
                                @endphp
                                <td class="px-3 py-3 align-top">
                    @if($entry)
                        @if($isAdmin)
                            <button type="button" wire:click="edit({{ $entry->id }})" onclick="document.getElementById('timetable-form')?.scrollIntoView({behavior: 'smooth', block: 'start'});" class="w-full rounded-lg border border-amber-200 bg-amber-50 px-2 py-2 text-left text-xs text-amber-900 hover:border-amber-300">
                                <div class="font-semibold">{{ $entry->subject?->name ?? 'Subject' }}</div>
                                <div class="mt-1 text-xs text-amber-800">
                                    {{ $entry->teacher?->name ?? 'No teacher' }}
                                    @if($entry->room)
                                        · {{ $entry->room }}
                                    @endif
                                </div>
                            </button>
                        @else
                            <div class="rounded-lg border border-gray-200 bg-white px-2 py-2 text-xs text-gray-700">
                                                <div class="font-semibold">{{ $entry->subject?->name ?? 'Subject' }}</div>
                                                <div class="mt-1 text-xs text-gray-500">
                                                    {{ $entry->teacher?->name ?? 'No teacher' }}
                                                    @if($entry->room)
                                                        · {{ $entry->room }}
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                    @else
                        @if($isAdmin)
                            <button type="button" wire:click="selectSlot({{ $d['day'] }}, @js($slot['start']), @js($slot['end']))" onclick="document.getElementById('timetable-form')?.scrollIntoView({behavior: 'smooth', block: 'start'});" class="w-full rounded-lg border border-dashed border-gray-200 bg-white px-2 py-2 text-left text-xs text-gray-500 hover:border-gray-300 hover:bg-gray-50">
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
            <div class="mt-4 rounded-xl border border-dashed border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                Pick a class from the filters to enable the grid.
            </div>
        @endif
    </div>

    <div class="card-padded">
        <div class="text-sm font-semibold text-gray-900">Schedule Details</div>
        <div class="mt-1 text-sm text-gray-600">Grouped by day.</div>

        <div class="mt-5 space-y-4">
            @forelse($grouped as $g)
                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                    <div class="text-sm font-bold text-gray-900">{{ $g['label'] }}</div>
                    <div class="mt-3 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-2 text-left">Time</th>
                                <th class="px-4 py-2 text-left">Class</th>
                                <th class="px-4 py-2 text-left">Section</th>
                                <th class="px-4 py-2 text-left">Subject</th>
                                <th class="px-4 py-2 text-left">Teacher</th>
                                <th class="px-4 py-2 text-left">Room</th>
                                @if($isAdmin)
                                    <th class="px-4 py-2 text-right">Actions</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                            @foreach($g['rows'] as $row)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ substr((string) $row->starts_at, 0, 5) }} - {{ substr((string) $row->ends_at, 0, 5) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row->schoolClass?->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row->section?->name ?? 'All' }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-gray-900">{{ $row->subject?->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row->teacher?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $row->room ?? '—' }}</td>
                                    @if($isAdmin)
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" wire:click="edit({{ $row->id }})" class="btn-outline">Edit</button>
                                                <button type="button" wire:click="delete({{ $row->id }})" onclick="return confirm('Delete entry?')" class="inline-flex items-center justify-center rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-700">
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
                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-600">
                    No timetable entries yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
