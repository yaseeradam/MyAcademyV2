@extends('layouts.app')

@section('content')
    @php($dbDriver = (string) config('database.default'))
    <div class="space-y-6" x-data="{ loading: false, success: false, error: false, message: '' }" @download-complete.window="loading = false">
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

                @error('backup_create')
                    <div class="mt-4 rounded-xl border border-orange-200 bg-orange-50 p-4 text-sm font-medium text-orange-800">
                        {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="{{ route('settings.backup.create') }}" class="mt-5" x-ref="backupForm" @submit="loading = true; setTimeout(() => { if (loading) loading = false; }, 3000)">
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

                <form method="POST" action="{{ route('settings.restore') }}" enctype="multipart/form-data" class="mt-5 space-y-4" @submit="if(confirm('This will overwrite current data. Continue?')) { loading = true; return true; } return false;">
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
                    >
                        Restore Backup
                    </button>
                </form>
            </div>
        </div>

        <!-- Loading Modal -->
        <div x-show="loading" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
            <div class="rounded-xl bg-white p-8 shadow-xl">
                <div class="flex items-center space-x-4">
                    <svg class="h-8 w-8 animate-spin text-blue-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <div>
                        <div class="text-lg font-semibold text-gray-900">Processing...</div>
                        <div class="text-sm text-gray-600">Please wait, this may take a few moments</div>
                    </div>
                </div>
            </div>
        </div>

        @if (session('status'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const el = document.querySelector('[x-data]').__x.$data;
                    el.success = true;
                    el.message = @json(session('status'));
                });
            </script>
        @endif

        @if ($errors->has('backup') || $errors->has('backup_create'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const el = document.querySelector('[x-data]').__x.$data;
                    el.error = true;
                    el.message = @json($errors->first('backup') ?: $errors->first('backup_create'));
                });
            </script>
        @endif

        <!-- Success Modal -->
        <div x-show="success" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="success = false">
            <div class="rounded-xl bg-white p-8 shadow-xl max-w-md">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Success</h3>
                        <p class="mt-2 text-sm text-gray-600" x-text="message"></p>
                        <button @click="success = false" class="mt-4 btn-primary">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div x-show="error" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" @click.self="error = false">
            <div class="rounded-xl bg-white p-8 shadow-xl max-w-md">
                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-10 w-10 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">Error</h3>
                        <p class="mt-2 text-sm text-gray-600" x-text="message"></p>
                        <button @click="error = false" class="mt-4 inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-600">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
