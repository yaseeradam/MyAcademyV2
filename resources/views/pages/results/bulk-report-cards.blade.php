@php
    /** @var \Illuminate\Support\Collection<int, \App\Models\SchoolClass> $classes */
    $user = auth()->user();
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="Bulk Report Cards" subtitle="Generate report cards for all students in a class at once." accent="results">
            <x-slot:actions>
                <a href="{{ route('examination') }}" class="btn-outline">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"/>
                    </svg>
                    Back
                </a>
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

        <div class="rounded-3xl border border-gray-100 bg-gradient-to-br from-indigo-50 to-purple-50/60 p-8 shadow-lg">
            <div class="flex items-center gap-4 mb-6">
                <div class="icon-3d grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-600 text-white shadow-xl shadow-indigo-500/30">
                    <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-black text-gray-900">Generate Class Report Cards</div>
                    <div class="mt-1 text-sm font-semibold text-gray-600">Select class, session, and term to download all report cards as a ZIP file</div>
                </div>
            </div>

            <form method="POST" action="{{ route('results.bulk-report-cards.generate') }}" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Class</label>
                        <select name="class_id" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-indigo-500" required>
                            <option value="">Select class</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(old('class_id') == $class->id)>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Session</label>
                        <input name="session" type="text" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-indigo-500" value="{{ old('session', config('myacademy.current_session', '2025/2026')) }}" placeholder="2025/2026" required />
                    </div>

                    <div>
                        <label class="text-xs font-bold uppercase tracking-wider text-gray-700">Term</label>
                        <select name="term" class="mt-2 w-full rounded-xl border-0 bg-white/80 px-4 py-3 text-sm font-semibold text-gray-900 ring-1 ring-white/60 backdrop-blur-sm focus:ring-2 focus:ring-indigo-500" required>
                            <option value="">Select term</option>
                            <option value="1" @selected(old('term') == '1')>Term 1</option>
                            <option value="2" @selected(old('term') == '2')>Term 2</option>
                            <option value="3" @selected(old('term') == '3')>Term 3</option>
                        </select>
                    </div>
                </div>

                <div class="rounded-xl border border-white/60 bg-white/50 p-5 backdrop-blur-sm">
                    <div class="flex items-start gap-3">
                        <svg class="h-5 w-5 text-indigo-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="16" x2="12" y2="12"/>
                            <line x1="12" y1="8" x2="12.01" y2="8"/>
                        </svg>
                        <div class="text-sm text-gray-700">
                            <strong class="font-bold">Note:</strong> This will generate individual PDF report cards for all students in the selected class and package them into a single ZIP file for download. Large classes may take a few moments to process.
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-3 text-sm font-bold text-white shadow-lg hover:shadow-xl transition-all">
                        <svg class="h-5 w-5 inline-block mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Generate Report Cards
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
