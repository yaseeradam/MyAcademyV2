@php
    use App\Models\Score;

    $scoreCount = Score::query()->count();
    $latestScoreAt = Score::query()->latest('updated_at')->value('updated_at');
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header
            title="Examination"
            subtitle="Enter scores, generate broadsheets, and download report cards."
            accent="results"
        >
            <x-slot:actions>
                <a href="{{ route('results.entry') }}" class="btn-primary">Enter Results</a>
                <a href="{{ route('results.broadsheet') }}" class="btn-outline">Broadsheet</a>
                @php($user = auth()->user())
                @if ($user?->hasPermission('results.publish'))
                    <a href="{{ route('results.bulk-report-cards') }}" class="btn-outline">Bulk Report Cards</a>
                @endif
            </x-slot:actions>
        </x-page-header>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <x-stat-card label="Score Records" :value="number_format((int) $scoreCount)" />
            <x-stat-card label="Last Updated" :value="$latestScoreAt ? \Illuminate\Support\Carbon::parse($latestScoreAt)->diffForHumans() : '—'" />
            <x-stat-card label="Report Cards" value="Download per student" iconBg="bg-purple-50" iconColor="text-purple-600" />
        </div>

        <div class="card-padded">
            <div class="text-sm font-semibold text-gray-900">What you can do here</div>
            <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="rounded-2xl bg-gray-50 p-5 ring-1 ring-inset ring-gray-100">
                    <div class="text-sm font-semibold text-gray-900">Score Entry</div>
                    <div class="mt-2 text-sm text-gray-600">
                        Capture CA1, CA2, and Exam scores. Grades are calculated automatically.
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('results.entry') }}" class="btn-primary">Open Score Entry</a>
                    </div>
                </div>
                <div class="rounded-2xl bg-gray-50 p-5 ring-1 ring-inset ring-gray-100">
                    <div class="text-sm font-semibold text-gray-900">Broadsheet</div>
                    <div class="mt-2 text-sm text-gray-600">
                        View class performance by subject, term, and session.
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('results.broadsheet') }}" class="btn-outline">Open Broadsheet</a>
                    </div>
                </div>
                <div class="rounded-2xl bg-gray-50 p-5 ring-1 ring-inset ring-gray-100">
                    <div class="text-sm font-semibold text-gray-900">Report Cards</div>
                    <div class="mt-2 text-sm text-gray-600">
                        Download printable report cards from any student profile (Results tab).
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
