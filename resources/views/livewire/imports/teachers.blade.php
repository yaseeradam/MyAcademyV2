<div class="space-y-6">
    <x-page-header title="Import Teachers" subtitle="CSV columns: name, email (optional: password, is_active)." accent="teachers">
        <x-slot:actions>
            <a href="{{ route('imports.index') }}" class="btn-outline">All Imports</a>
        </x-slot:actions>
    </x-page-header>

    <div class="card-padded space-y-4">
        <div>
            <label class="text-xs font-semibold uppercase tracking-wider text-gray-500">CSV File</label>
            <input wire:model="file" type="file" accept=".csv,text/csv" class="mt-2" />
            @error('file')
                <div class="mt-2 text-sm font-semibold text-orange-700">{{ $message }}</div>
            @enderror
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model.live="updateExisting" />
                Update existing teachers
            </label>
            <label class="flex items-center gap-2 text-sm text-gray-700">
                <input type="checkbox" wire:model.live="defaultActive" />
                Default active (when not provided)
            </label>
        </div>

        <div class="flex flex-wrap gap-2">
            <button type="button" wire:click="analyze" class="btn-outline" @disabled(! $file)>Analyze</button>
            <button type="button" wire:click="import" class="btn-primary" @disabled(! $file)>Import</button>
        </div>

        @if ($summary)
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-5">
                <x-stat-card label="Valid Rows" :value="number_format((int) ($summary['rows_valid'] ?? 0))" />
                <x-stat-card label="Create" :value="number_format((int) ($summary['to_create'] ?? 0))" />
                <x-stat-card label="Update" :value="number_format((int) ($summary['to_update'] ?? 0))" />
                <x-stat-card label="Skip Existing" :value="number_format((int) ($summary['to_skip_existing'] ?? 0))" />
                <x-stat-card label="Errors" :value="number_format((int) ($summary['errors'] ?? 0))" />
            </div>
        @endif

        @if ($errorsPreview)
            <div class="rounded-xl border border-orange-200 bg-orange-50/60 p-4">
                <div class="text-sm font-semibold text-orange-900">CSV issues</div>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-orange-900">
                    @foreach ($errorsPreview as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>

