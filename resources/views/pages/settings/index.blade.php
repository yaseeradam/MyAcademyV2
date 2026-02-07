@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Settings" subtitle="System configuration and offline backups." accent="settings" />

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <a
                href="{{ route('settings.backup') }}"
                class="card p-6 transition hover:ring-brand-100"
            >
                <div class="text-sm font-semibold text-gray-900">Backup & Restore</div>
                <div class="mt-2 text-sm text-gray-600">
                    Create a snapshot ZIP (database + uploads) and restore from USB.
                </div>
            </a>
        </div>
    </div>
@endsection
