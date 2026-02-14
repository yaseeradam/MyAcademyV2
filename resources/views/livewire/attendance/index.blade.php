<div class="space-y-6" x-data="{ showModal: false, studentId: null, studentName: '' }">
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
            <button type="button" wire:click="save" class="btn-outline" @disabled(! $classId)>
                Save Attendance
            </button>

            <div class="ml-auto flex flex-wrap items-center gap-2">
                <button type="button" wire:click="markAll('Present')" class="rounded-lg bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-100" @disabled(! $classId)>
                    All Present
                </button>
                <button type="button" wire:click="markAll('Absent')" class="rounded-lg bg-red-50 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-100" @disabled(! $classId)>
                    All Absent
                </button>
                <button type="button" wire:click="markAll('Late')" class="rounded-lg bg-orange-50 px-3 py-2 text-xs font-semibold text-orange-700 hover:bg-orange-100" @disabled(! $classId)>
                    All Late
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

    <x-table>
        <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
            <tr>
                <th class="px-5 py-3">#</th>
                <th class="px-5 py-3">Student</th>
                <th class="px-5 py-3">Admission No</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3">Note</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($this->students as $index => $student)
                <tr class="bg-white hover:bg-gray-50">
                    <td class="px-5 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                    <td class="px-5 py-4">
                        <div class="text-sm font-semibold text-gray-900">{{ $student->full_name }}</div>
                    </td>
                    <td class="px-5 py-4 text-sm text-gray-700">{{ $student->admission_number }}</td>
                    <td class="px-5 py-4">
                        <div class="flex gap-2">
                            <button type="button" wire:click="$set('marks.{{ $student->id }}.status', 'Present')" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ ($marks[$student->id]['status'] ?? 'Present') === 'Present' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                P
                            </button>
                            <button type="button" wire:click="$set('marks.{{ $student->id }}.status', 'Absent')" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ ($marks[$student->id]['status'] ?? 'Present') === 'Absent' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                A
                            </button>
                            <button type="button" wire:click="$set('marks.{{ $student->id }}.status', 'Late')" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ ($marks[$student->id]['status'] ?? 'Present') === 'Late' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                L
                            </button>
                            <button type="button" wire:click="$set('marks.{{ $student->id }}.status', 'Excused')" class="rounded-lg px-3 py-1.5 text-xs font-semibold {{ ($marks[$student->id]['status'] ?? 'Present') === 'Excused' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                E
                            </button>
                        </div>
                    </td>
                    <td class="px-5 py-4">
                        <button type="button" @click="showModal = true; studentId = {{ $student->id }}; studentName = '{{ $student->full_name }}'" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                            {{ isset($marks[$student->id]['note']) && $marks[$student->id]['note'] ? 'Edit note' : 'Add note' }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">
                        Select a class and click "Load Students" to begin.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <!-- Note Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="showModal = false">
        <div class="rounded-xl bg-white p-6 shadow-xl max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Note</h3>
            <p class="mt-1 text-sm text-gray-600" x-text="studentName"></p>
            
            <div class="mt-4">
                <textarea 
                    :wire:model="'marks.' + studentId + '.note'"
                    rows="3" 
                    placeholder="Enter optional note..."
                    class="w-full rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700 focus:border-brand-500 focus:ring-brand-500"
                ></textarea>
            </div>

            <div class="mt-4 flex gap-3">
                <button @click="showModal = false" class="btn-primary flex-1">
                    Save
                </button>
                <button @click="showModal = false" class="btn-outline flex-1">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>
