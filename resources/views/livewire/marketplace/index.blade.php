@php
    $state = $this->licenseState;
    $ok = (bool) ($state['ok'] ?? false);
    $data = (array) ($state['data'] ?? []);
    $features = $ok ? ($data['features'] ?? []) : [];
    if (! is_array($features)) {
        $features = [];
    }

    $hasCbt = $ok && in_array('cbt', $features, true);
    $hasSavingsLoan = $ok && in_array('savings_loan', $features, true);

    $expiresAt = $ok ? ($data['expires_at'] ?? null) : null;
    $deviceLimit = $ok ? (int) ($data['device_limit'] ?? 0) : 0;
@endphp

<div class="space-y-6">
    <x-page-header title="Marketplace" subtitle="Unlock premium modules with a license key (offline/LAN)." accent="more">
        <x-slot:actions>
            <a href="{{ route('more-features') }}" class="btn-outline">Back</a>
        </x-slot:actions>
    </x-page-header>

    @if ($errors->has('premium'))
        <div class="card-padded border border-orange-200 bg-orange-50/60 text-sm text-orange-900">
            {{ $errors->first('premium') }}
        </div>
    @endif

    <div class="card-padded">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <div class="text-sm font-semibold text-gray-900">License Status</div>
                @if ($ok)
                    <div class="mt-1 text-sm text-gray-600">
                        Installed &middot; Device limit: {{ $deviceLimit ?: '-' }}
                        @if ($expiresAt)
                            &middot; Expires: {{ \Illuminate\Support\Carbon::parse($expiresAt)->format('M j, Y') }}
                        @endif
                    </div>
                @else
                    <div class="mt-1 text-sm text-gray-600">{{ (string) ($state['reason'] ?? 'No license installed.') }}</div>
                @endif
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <x-status-badge variant="{{ $ok ? 'success' : 'warning' }}">{{ $ok ? 'Licensed' : 'Locked' }}</x-status-badge>
                @if ($ok)
                    <x-status-badge variant="neutral">{{ count($features) }} feature(s)</x-status-badge>
                @endif
            </div>
        </div>

        @if ($ok)
            <div class="mt-4 rounded-2xl border border-gray-100 bg-white p-4">
                <div class="text-xs font-semibold uppercase tracking-wider text-gray-500">Enabled Features</div>
                <div class="mt-2 flex flex-wrap gap-2">
                    @forelse ($features as $f)
                        <span class="rounded-full bg-slate-50 px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">{{ $f }}</span>
                    @empty
                        <span class="text-sm text-gray-600">No features in this license.</span>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div class="card-padded">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-lg font-black text-gray-900">CBT</div>
                    <div class="mt-1 text-sm font-semibold text-gray-600">Teachers set questions &rarr; admin approves &rarr; students take exams.</div>
                </div>
                <x-status-badge variant="{{ $hasCbt ? 'success' : 'warning' }}">{{ $hasCbt ? 'Enabled' : 'Disabled' }}</x-status-badge>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($hasCbt)
                    <a href="{{ route('cbt.index') }}" class="btn-primary">Open CBT</a>
                @else
                    <button type="button" wire:click="selectFeature('cbt')" class="btn-primary">Upload license</button>
                @endif
            </div>
        </div>

        <div class="card-padded">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <div class="text-lg font-black text-gray-900">Savings / Loan</div>
                    <div class="mt-1 text-sm font-semibold text-gray-600">Savings and loan management module (premium).</div>
                </div>
                <x-status-badge variant="{{ $hasSavingsLoan ? 'success' : 'warning' }}">{{ $hasSavingsLoan ? 'Enabled' : 'Disabled' }}</x-status-badge>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-2">
                @if ($hasSavingsLoan)
                    <a href="{{ route('savings-loan.index') }}" class="btn-primary">Open</a>
                @else
                    <button type="button" wire:click="selectFeature('savings_loan')" class="btn-primary">Upload license</button>
                @endif
            </div>
        </div>
    </div>

    @if ($selectedFeature)
        <div class="card-padded border border-brand-100 bg-brand-50/40">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="text-sm font-semibold text-gray-900">Install License</div>
                    <div class="mt-1 text-sm text-gray-600">
                        Upload the license file that enables
                        <span class="font-semibold">{{ $selectedFeature === 'cbt' ? 'CBT' : 'Savings/Loan' }}</span>.
                    </div>
                </div>
                <button type="button" wire:click="cancelInstall" class="btn-outline">Cancel</button>
            </div>

            <form wire:submit="install" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-6">
                <div class="sm:col-span-5">
                    <input wire:model="licenseFile" type="file" accept=".json,.txt,application/json,text/plain" class="block w-full text-sm text-gray-700" />
                    @error('licenseFile') <div class="mt-1 text-xs font-semibold text-orange-700">{{ $message }}</div> @enderror
                </div>
                <div class="sm:col-span-1 flex items-end">
                    <button type="submit" class="btn-primary w-full justify-center">Install</button>
                </div>
            </form>
        </div>
    @endif
</div>

