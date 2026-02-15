<div class="space-y-6">
    <x-page-header title="Audit Logs" subtitle="Track important actions (backup, results approvals, billing, user changes)." accent="settings">
        <x-slot:actions>
            <a href="{{ route('settings.index') }}" class="btn-outline">Back</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card-padded">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">Action</label>
                <input wire:model.live.debounce.300ms="action" type="text" placeholder="e.g. billing.transaction_voided" class="mt-2 input-compact" />
            </div>

            <div class="lg:col-span-2">
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">User</label>
                <select wire:model.live="userId" class="mt-2 select">
                    <option value="">All</option>
                    @foreach ($this->users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">From</label>
                <input wire:model.live="from" type="date" class="mt-2 input-compact" />
            </div>

            <div>
                <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">To</label>
                <input wire:model.live="to" type="date" class="mt-2 input-compact" />
            </div>
        </div>
    </div>

    <div class="card-padded">
        <div class="text-sm font-semibold text-gray-900">Recent Activity</div>
        <div class="mt-4">
            <x-table class="text-xs">
                <thead class="bg-gray-50 text-[11px] font-semibold uppercase tracking-wider text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">Action</th>
                        <th class="px-5 py-3">Target</th>
                        <th class="px-5 py-3">Meta</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($this->logs as $log)
                        <tr class="bg-white hover:bg-gray-50">
                            <td class="px-5 py-4 whitespace-nowrap text-gray-700">
                                {{ $log->created_at?->format('M j, Y g:i A') }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $log->user?->name ?? 'System' }}</div>
                                <div class="mt-1 text-[11px] text-gray-500">
                                    {{ $log->user?->email ?? '-' }}
                                    @if($log->user?->role)
                                        / {{ $log->user->role }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 font-mono text-[11px] text-gray-800">{{ $log->action }}</td>
                            <td class="px-5 py-4 font-mono text-[11px] text-gray-700">
                                @if($log->auditable_type)
                                    {{ class_basename($log->auditable_type) }}#{{ $log->auditable_id }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                @if(is_array($log->meta) && $log->meta !== [])
                                    <pre class="max-w-[28rem] overflow-x-auto whitespace-pre-wrap rounded-xl bg-slate-50 p-3 text-[10px] text-slate-700 ring-1 ring-inset ring-slate-200">{{ json_encode($log->meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">No audit logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </x-table>
        </div>

        <div class="mt-4">
            {{ $this->logs->links() }}
        </div>
    </div>
</div>

