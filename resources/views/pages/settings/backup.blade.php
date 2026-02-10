@extends('layouts.app')

@section('content')
    @php($dbDriver = (string) config('database.default'))
    <div class="space-y-6">
        <x-page-header
            title="Backup & Restore"
            subtitle="Offline snapshot system: downloads a zip with database + uploads + school settings."
            accent="settings"
        >
            <x-slot:actions>
                <a href="{{ route('settings') }}" class="btn-outline">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
            </x-slot:actions>
        </x-page-header>

        @if (session('status'))
            <div class="rounded-xl border border-green-100 bg-green-50 p-4 text-sm font-medium text-green-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="card-padded">
                <div class="text-sm font-semibold text-gray-900">Create Backup</div>
                <div class="mt-2 text-sm text-gray-600">
                    Creates a zip containing the database, uploads folder, and school settings.
                </div>

                <form method="POST" action="{{ route('settings.backup.create') }}" class="mt-5">
                    @csrf
                    <button
                        type="submit"
                        class="btn-primary"
                    >
                        Backup Now
                    </button>
                </form>

                <div class="mt-4 text-xs text-gray-500">
                    @if ($dbDriver === 'mysql')
                        Uses <span class="font-mono">mysqldump</span>. Configure binaries with <span class="font-mono">MYACADEMY_MYSQLDUMP</span> and <span class="font-mono">MYACADEMY_MYSQL</span> if they are not in PATH.
                    @elseif ($dbDriver === 'sqlite')
                        Uses your SQLite file: <span class="font-mono">{{ config('database.connections.sqlite.database') }}</span>
                    @else
                        Database driver <span class="font-mono">{{ $dbDriver }}</span> is not supported for backup/restore.
                    @endif
                </div>
            </div>

            <div class="card-padded">
                <div class="text-sm font-semibold text-gray-900">Restore Backup</div>
                <div class="mt-2 text-sm text-gray-600">
                    Upload a backup zip. The system enters maintenance mode, replaces the database and uploads, then restores settings.
                </div>

                <form method="POST" action="{{ route('settings.restore') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf

                    <input
                        type="file"
                        name="backup"
                        accept=".zip"
                        class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200"
                    />
                    @error('backup')
                        <div class="text-sm text-orange-700">{{ $message }}</div>
                    @enderror

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-600"
                        onclick="return confirm('This will overwrite current data. Continue?')"
                    >
                        Restore Backup
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
