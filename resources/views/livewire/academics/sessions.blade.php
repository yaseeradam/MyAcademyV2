<div class="space-y-6">
    <x-page-header title="Academic Sessions" subtitle="Manage academic years (sessions) and set the active one." accent="more">
        <x-slot:actions>
            <a href="{{ route('more-features') }}" class="btn-outline">Back</a>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        <div class="card-padded">
            <div class="text-sm font-semibold text-gray-900">Create / Edit</div>
            <div class="mt-1 text-sm text-gray-600">Example: 2026/2027</div>

            <div class="mt-4 space-y-4">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Session</label>
                    <input wire:model="name" class="mt-2 input w-full" placeholder="2026/2027" />
                    @error('name') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Start date (optional)</label>
                        <input wire:model="startsOn" type="date" class="mt-2 input w-full" />
                        @error('startsOn') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">End date (optional)</label>
                        <input wire:model="endsOn" type="date" class="mt-2 input w-full" />
                        @error('endsOn') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                    </div>
                </div>

                <label class="flex items-center gap-3 text-sm font-semibold text-gray-700">
                    <input wire:model="isActive" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                    Set as active session
                </label>

                <div class="flex items-center gap-2">
                    <button type="button" wire:click="save" class="btn-primary">
                        {{ $editingId ? 'Update' : 'Create' }}
                    </button>
                    <button type="button" wire:click="clearForm" class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-white text-indigo-600 border-2 border-indigo-200 hover:bg-indigo-50">Clear</button>
                </div>
            </div>
        </div>

        <div class="card-padded">
            <div class="text-sm font-semibold text-gray-900">All Sessions</div>
            <div class="mt-1 text-sm text-gray-600">Only one can be active.</div>

            <div class="mt-4 space-y-2">
                @forelse($sessions as $s)
                    <div class="rounded-2xl border border-gray-100 bg-white p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <div class="text-sm font-bold text-gray-900">{{ $s->name }}</div>
                                    @if($s->is_active)
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-emerald-700 ring-1 ring-emerald-100">Active</span>
                                    @endif
                                </div>
                                <div class="mt-1 text-xs text-gray-500">
                                    @if($s->starts_on) {{ $s->starts_on->toDateString() }} @endif
                                    @if($s->starts_on && $s->ends_on) → @endif
                                    @if($s->ends_on) {{ $s->ends_on->toDateString() }} @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(! $s->is_active)
                                    <button type="button" wire:click="setActive({{ $s->id }})" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">
                                        Set active
                                    </button>
                                @endif
                                <button type="button" wire:click="edit({{ $s->id }})" class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-white text-indigo-600 border-2 border-indigo-200 hover:bg-indigo-50">Edit</button>
                                <button type="button" wire:click="delete({{ $s->id }})" class="inline-flex items-center justify-center rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-700">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-600">
                        No sessions yet.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

