@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Add Teacher" subtitle="Create a teacher account for access to academics modules." accent="teachers">
            <x-slot:actions>
                <a href="{{ route('teachers') }}" class="btn-ghost">Back to Teachers</a>
            </x-slot:actions>
        </x-page-header>

        @if (session('status'))
            <div class="card-padded border border-green-200 bg-green-50/60 text-sm text-green-900">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="card-padded border border-orange-200 bg-orange-50/60">
                <div class="text-sm font-semibold text-orange-900">Please fix the following:</div>
                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-orange-900">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('teachers.store') }}" class="card-padded" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div>
                    <label class="text-sm font-semibold text-slate-900">Full name</label>
                    <div class="mt-2">
                        <input
                            name="name"
                            class="input"
                            value="{{ old('name') }}"
                            placeholder="e.g., Mrs. Anita Okoye"
                            required
                            autocomplete="name"
                        />
                    </div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-900">Email</label>
                    <div class="mt-2">
                        <input
                            name="email"
                            type="email"
                            class="input"
                            value="{{ old('email') }}"
                            placeholder="e.g., anita@school.edu"
                            required
                            autocomplete="email"
                        />
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <label class="text-sm font-semibold text-slate-900">Profile photo</label>
                    <div class="mt-2">
                        <input
                            name="photo"
                            type="file"
                            accept="image/*"
                            class="block w-full text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200"
                        />
                    </div>
                    <div class="mt-2 text-xs text-slate-500">Optional. Used on teacher cards and profile page.</div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-900">Password</label>
                    <div class="mt-2">
                        <input
                            name="password"
                            type="password"
                            class="input"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                    <div class="mt-2 text-xs text-slate-500">Minimum 8 characters.</div>
                </div>

                <div>
                    <label class="text-sm font-semibold text-slate-900">Confirm password</label>
                    <div class="mt-2">
                        <input
                            name="password_confirmation"
                            type="password"
                            class="input"
                            required
                            autocomplete="new-password"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-6 flex flex-col gap-3 border-t border-gray-200/70 pt-6 sm:flex-row sm:items-center sm:justify-between">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-slate-700">
                    <input
                        type="checkbox"
                        class="checkbox-custom"
                        name="is_active"
                        value="1"
                        @checked(old('is_active', true))
                    />
                    Active (can log in)
                </label>

                <div class="flex items-center gap-2">
                    <a href="{{ route('teachers') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 5v14" />
                            <path d="M5 12h14" />
                        </svg>
                        Add Teacher
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
