<div class="space-y-6">
    <x-page-header title="Announcements" subtitle="School-wide notices for staff and students." accent="more">
        <x-slot:actions>
            <a href="{{ route('more-features') }}" class="btn-outline">Back</a>
        </x-slot:actions>
    </x-page-header>

    @if ($isAdmin)
        <div class="card-padded">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="min-w-0">
                    <div class="text-sm font-semibold text-gray-900">Create / Edit</div>
                    <div class="mt-1 text-sm text-gray-600">Draft announcements, then publish when ready.</div>
                </div>

                <div class="w-full lg:max-w-2xl">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                        <div class="lg:col-span-2">
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Title</label>
                            <input wire:model="title" class="mt-2 input w-full" placeholder="Announcement title" />
                            @error('title') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Audience</label>
                            <select wire:model="audience" class="mt-2 select w-full">
                                <option value="all">All</option>
                                <option value="staff">Staff</option>
                                <option value="admin">Admins</option>
                                <option value="teacher">Teachers</option>
                                <option value="bursar">Bursars</option>
                            </select>
                            @error('audience') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Body</label>
                        <textarea wire:model="body" rows="5" class="mt-2 w-full rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 shadow-sm focus:border-brand-500 focus:ring-brand-500" placeholder="Write the announcement..."></textarea>
                        @error('body') <div class="mt-1 text-xs text-orange-700">{{ $message }}</div> @enderror
                    </div>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <button type="button" wire:click="save" class="btn-primary">
                            {{ $editingId ? 'Update Draft' : 'Save Draft' }}
                        </button>
                        <button type="button" wire:click="clearForm" class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-white text-indigo-600 border-2 border-indigo-200 hover:bg-indigo-50">Clear</button>
                        @if ($editingId)
                            <button type="button" wire:click="publish({{ $editingId }})" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">
                                Publish
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card-padded">
        <div class="text-sm font-semibold text-gray-900">All Announcements</div>
        <div class="mt-1 text-sm text-gray-600">Latest first.</div>

        <div class="mt-5 space-y-3">
            @forelse($announcements as $a)
                <div class="rounded-2xl border border-gray-100 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-gray-900">{{ $a->title }}</div>
                            <div class="mt-1 text-xs text-gray-500">
                                Audience: <span class="font-mono">{{ $a->audience }}</span>
                                •
                                @if($a->published_at)
                                    Published {{ $a->published_at->diffForHumans() }}
                                @else
                                    Draft
                                @endif
                            </div>
                        </div>

                        @if ($isAdmin)
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="edit({{ $a->id }})" class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-white text-indigo-600 border-2 border-indigo-200 hover:bg-indigo-50">Edit</button>
                                @if($a->published_at)
                                    <button type="button" wire:click="unpublish({{ $a->id }})" class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-white text-indigo-600 border-2 border-indigo-200 hover:bg-indigo-50">Unpublish</button>
                                @else
                                    <button type="button" wire:click="publish({{ $a->id }})" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-emerald-700">
                                        Publish
                                    </button>
                                @endif
                                <button type="button" wire:click="delete({{ $a->id }})" class="inline-flex items-center justify-center rounded-lg bg-rose-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-rose-700">
                                    Delete
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="mt-3 whitespace-pre-wrap text-sm text-gray-700">{{ $a->body }}</div>
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-6 text-center text-sm text-gray-600">
                    No announcements yet.
                </div>
            @endforelse
        </div>
    </div>
</div>

