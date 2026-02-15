<div class="space-y-6">
    <x-page-header title="Premium Devices" subtitle="Manage registered devices (max 2 removals per 30 days)." accent="settings">
        <x-slot:actions>
            <a href="{{ route('settings.index') }}" class="btn-outline">Back</a>
        </x-slot:actions>
    </x-page-header>

    @error('device') <div class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm font-semibold text-rose-800">{{ $message }}</div> @enderror

    <div class="card-padded">
        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Current Device ID</div>
                <div class="mt-2 font-mono text-xs text-gray-800 break-all">{{ $this->currentDeviceId }}</div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Removals Used (last {{ $this->removalWindowDays }} days)</div>
                <div class="mt-2 text-2xl font-black text-slate-900">{{ $this->removalsInWindow }} / {{ $this->removalLimit }}</div>
                @if($this->nextRemovalResetAt)
                    <div class="mt-1 text-xs text-gray-500">Next reset: {{ $this->nextRemovalResetAt->format('M j, Y g:i A') }}</div>
                @endif
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Devices</div>
                <div class="mt-2 text-2xl font-black text-slate-900">{{ $this->devices->count() }}</div>
            </div>
        </div>
    </div>

    <div class="card-padded">
        <div class="text-sm font-semibold text-gray-900">Registered Devices</div>
        <div class="mt-1 text-sm text-gray-600">Removing a device frees up a slot, but is limited to 2 removals per rolling 30 days.</div>

        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3 text-left">Label</th>
                        <th class="px-5 py-3 text-left">Device ID</th>
                        <th class="px-5 py-3 text-left">Last Seen</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($this->devices as $d)
                        @php($isCurrent = $d->device_id === $this->currentDeviceId)
                        <tr class="{{ $d->revoked_at ? 'bg-gray-50' : '' }}">
                            <td class="px-5 py-4 text-sm font-semibold text-gray-900">
                                {{ $d->label ?: 'Device' }}
                                @if($isCurrent)
                                    <span class="ml-2 inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">CURRENT</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-xs font-mono text-gray-700">{{ $d->device_id }}</td>
                            <td class="px-5 py-4 text-sm text-gray-700">{{ $d->last_seen_at?->format('M j, Y g:i A') ?: '-' }}</td>
                            <td class="px-5 py-4 text-sm">
                                @if($d->revoked_at)
                                    <span class="inline-flex items-center rounded-full bg-rose-100 px-2 py-0.5 text-xs font-bold text-rose-800">Removed</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-sky-100 px-2 py-0.5 text-xs font-bold text-sky-800">Active</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if(!$d->revoked_at)
                                    <button
                                        type="button"
                                        class="btn-outline"
                                        wire:click="removeDevice({{ $d->id }})"
                                        onclick="return confirm('Remove this device? This counts toward your 2 removals per 30 days.')"
                                        {{ $isCurrent ? 'disabled' : '' }}
                                    >
                                        Remove
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">No devices registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

