@php
    $user = auth()->user();
@endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <x-page-header title="More Features" subtitle="All modules and utilities grouped in one place." accent="more" />

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('students.index') }}" class="card p-6 transition hover:ring-brand-100">
                <div class="text-sm font-semibold text-gray-900">Students</div>
                <div class="mt-2 text-sm text-gray-600">Profiles, results, attendance, and finance.</div>
            </a>

            @if ($user?->role === 'admin' || $user?->role === 'teacher')
                <a href="{{ route('attendance') }}" class="card p-6 transition hover:ring-brand-100">
                    <div class="text-sm font-semibold text-gray-900">Attendance</div>
                    <div class="mt-2 text-sm text-gray-600">Take daily attendance and view history.</div>
                </a>
            @endif

            @if ($user?->role === 'admin' || $user?->role === 'bursar')
                <a href="{{ route('billing.index') }}" class="card p-6 transition hover:ring-brand-100">
                    <div class="text-sm font-semibold text-gray-900">Billing</div>
                    <div class="mt-2 text-sm text-gray-600">Transactions, debtors, receipts, and exports.</div>
                </a>
            @endif

            @if ($user?->role === 'admin' || $user?->role === 'teacher')
                <a href="{{ route('institute') }}" class="card p-6 transition hover:ring-brand-100">
                    <div class="text-sm font-semibold text-gray-900">Institute</div>
                    <div class="mt-2 text-sm text-gray-600">Classes, sections, subjects, and allocations.</div>
                </a>
            @endif

            @if ($user?->role === 'admin')
                <a href="{{ route('users.index') }}" class="card p-6 transition hover:ring-brand-100">
                    <div class="text-sm font-semibold text-gray-900">User Management</div>
                    <div class="mt-2 text-sm text-gray-600">Create accounts, assign roles, activate/deactivate.</div>
                </a>

                <a href="{{ route('imports.index') }}" class="card p-6 transition hover:ring-brand-100">
                    <div class="text-sm font-semibold text-gray-900">Imports</div>
                    <div class="mt-2 text-sm text-gray-600">Bulk import students, teachers, and subjects (CSV).</div>
                </a>
            @endif

            @if ($user?->role === 'admin')
                <a href="{{ route('settings') }}" class="card p-6 transition hover:ring-brand-100">
                    <div class="text-sm font-semibold text-gray-900">Settings</div>
                    <div class="mt-2 text-sm text-gray-600">Backup, restore, and system preferences.</div>
                </a>
            @endif
        </div>
    </div>
@endsection
