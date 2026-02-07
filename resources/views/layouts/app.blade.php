<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body class="h-full bg-[#F5F7FA] text-slate-900 transition-colors duration-300 dark:bg-dark-50 dark:text-dark-900">
        <div id="app" class="min-h-screen">
            <!-- Mobile Sidebar Overlay -->
            <div id="mobileSidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>
            
            <!-- Mobile Sidebar -->
            <aside id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 w-80 transform -translate-x-full transition-transform duration-300 lg:hidden">
                <div class="flex h-full flex-col bg-white dark:bg-dark-100">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-dark-200 px-4 py-4">
                        <div class="flex items-center gap-3">
                            <div class="grid h-10 w-10 place-items-center rounded-xl bg-blue-50 text-blue-600">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 3 1 9l11 6 9-4.91V17a2 2 0 0 1-1.1 1.79l-7.4 3.7a2 2 0 0 1-1.8 0l-7.4-3.7A2 2 0 0 1 2 17V9" />
                                    <path d="M12 21V9" />
                                </svg>
                            </div>
                            <div class="text-sm font-semibold tracking-tight text-slate-900">
                                {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}
                            </div>
                        </div>
                        <button id="closeMobileSidebar" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Mobile Navigation Grid -->
                    <div class="flex-1 overflow-y-auto p-4">
                        <div class="grid grid-cols-2 gap-3">
                            @php($user = auth()->user())
                            
                            <a href="{{ route('dashboard') }}" class="card-interactive p-4 text-center">
                                <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-indigo-50 text-indigo-600">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M3 13h8V3H3v10zM13 21h8V11h-8v10zM13 3h8v6h-8V3zM3 21h8v-6H3v6z"/>
                                    </svg>
                                </div>
                                <div class="text-xs font-semibold text-gray-700">Dashboard</div>
                            </a>
                            
                            @if ($user?->role === 'admin' || $user?->role === 'teacher')
                                <a href="{{ route('institute') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-purple-50 text-purple-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                                            <polyline points="17 21 17 13 7 13 7 21" />
                                            <polyline points="7 3 7 8 15 8" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Institute</div>
                                </a>
                            @endif
                            
                            <a href="{{ route('students.index') }}" class="card-interactive p-4 text-center">
                                <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-blue-50 text-blue-600">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                        <circle cx="9" cy="7" r="4" />
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                    </svg>
                                </div>
                                <div class="text-xs font-semibold text-gray-700">Students</div>
                            </a>

                            <a href="{{ route('more-features') }}" class="card-interactive p-4 text-center">
                                <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-slate-50 text-slate-700">
                                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 6v.01" />
                                        <path d="M12 12v.01" />
                                        <path d="M12 18v.01" />
                                        <path d="M7 6v.01" />
                                        <path d="M7 12v.01" />
                                        <path d="M7 18v.01" />
                                        <path d="M17 6v.01" />
                                        <path d="M17 12v.01" />
                                        <path d="M17 18v.01" />
                                    </svg>
                                </div>
                                <div class="text-xs font-semibold text-gray-700">More</div>
                            </a>
                            
                            @if ($user?->role === 'admin' || $user?->role === 'teacher')
                                <a href="{{ route('teachers') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-orange-50 text-orange-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M2 7l10-5 10 5-10 5L2 7z" />
                                            <path d="M12 12v10" />
                                            <path d="M22 7v10l-10 5" />
                                            <path d="M2 7v10l10 5" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Teachers</div>
                                </a>

                                <a href="{{ route('classes.index') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-slate-50 text-slate-700">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 4h16v16H4z" />
                                            <path d="M8 8h8" />
                                            <path d="M8 12h8" />
                                            <path d="M8 16h5" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Classes</div>
                                </a>

                                <a href="{{ route('subjects.index') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-slate-50 text-slate-700">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                            <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5V4.5A2.5 2.5 0 0 1 6.5 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Subjects</div>
                                </a>
                                
                                <a href="{{ route('results.entry') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-green-50 text-green-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                            <polyline points="14 2 14 8 20 8" />
                                            <line x1="16" y1="13" x2="8" y2="13" />
                                            <line x1="16" y1="17" x2="8" y2="17" />
                                            <polyline points="10 9 9 9 8 9" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Scores</div>
                                </a>
                                
                                <a href="{{ route('attendance') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-blue-50 text-blue-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Attendance</div>
                                </a>
                                
                                <a href="{{ route('examination') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-indigo-50 text-indigo-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M8 2v4M16 2v4M3 10h18M3 22h18a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Exams</div>
                                </a>
                            @endif
                            
                            @if ($user?->role === 'admin' || $user?->role === 'bursar')
                                <a href="{{ route('billing.index') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-purple-50 text-purple-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 1v22" />
                                            <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Billing</div>
                                </a>
                                
                                <a href="{{ route('accounts') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-yellow-50 text-yellow-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Accounts</div>
                                </a>
                            @endif
                            
                            @if ($user?->role === 'admin')
                                <a href="{{ route('settings') }}" class="card-interactive p-4 text-center">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-gray-50 text-gray-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="3" />
                                            <path d="M12 1v6M12 17v6M4.22 4.22l4.24 4.24M15.54 15.54l4.24 4.24M1 12h6M17 12h6M4.22 19.78l4.24-4.24M15.54 8.46l4.24-4.24" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">Settings</div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </aside>
            
            <!-- Desktop Sidebar -->
            <aside class="fixed inset-y-0 left-0 hidden w-64 flex-col border-r border-gray-200/70 bg-white lg:flex">
                <div class="flex items-center gap-3 border-b border-gray-200/70 px-6 py-5">
                    <div class="grid h-11 w-11 place-items-center rounded-xl bg-brand-50 text-brand-700 ring-1 ring-inset ring-brand-100">
                        <svg
                            class="h-6 w-6"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M12 3 1 9l11 6 9-4.91V17a2 2 0 0 1-1.1 1.79l-7.4 3.7a2 2 0 0 1-1.8 0l-7.4-3.7A2 2 0 0 1 2 17V9" />
                            <path d="M12 21V9" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-base font-semibold tracking-tight text-slate-900">
                            {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}
                        </div>
                        <div class="mt-0.5 text-xs text-slate-500">
                            {{ config('myacademy.current_term', 'Term 1') }} · {{ config('myacademy.current_week', 'Week 1') }}
                        </div>
                    </div>
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-4">
                    @php($user = auth()->user())

                    <a
                        href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                    >
                        <svg class="{{ request()->routeIs('dashboard') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M3 13h8V3H3v10zM13 21h8V11h-8v10zM13 3h8v6h-8V3zM3 21h8v-6H3v6z" />
                        </svg>
                        Dashboard
                    </a>

                    <a
                        href="{{ route('students.index') }}"
                        class="{{ request()->routeIs('students.*') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                    >
                        <svg class="{{ request()->routeIs('students.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                            <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                        Students
                    </a>

                    @if ($user?->role === 'admin' || $user?->role === 'teacher')
                        <a
                            href="{{ route('institute') }}"
                            class="{{ request()->routeIs('institute') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('institute') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16v16H4z" />
                                <path d="M8 4v16" />
                                <path d="M4 8h16" />
                            </svg>
                            Institute
                        </a>

                        <a
                            href="{{ route('teachers') }}"
                            class="{{ request()->routeIs('teachers') || request()->routeIs('teachers.*') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('teachers') || request()->routeIs('teachers.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 7l10-5 10 5-10 5L2 7z" />
                                <path d="M12 12v10" />
                                <path d="M22 7v10l-10 5" />
                                <path d="M2 7v10l10 5" />
                            </svg>
                            Teachers
                        </a>

                        <a
                            href="{{ route('classes.index') }}"
                            class="{{ request()->routeIs('classes.*') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('classes.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16v16H4z" />
                                <path d="M8 8h8" />
                                <path d="M8 12h8" />
                                <path d="M8 16h5" />
                            </svg>
                            Classes
                        </a>

                        <a
                            href="{{ route('subjects.index') }}"
                            class="{{ request()->routeIs('subjects.*') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('subjects.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20" />
                                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5V4.5A2.5 2.5 0 0 1 6.5 2z" />
                            </svg>
                            Subjects
                        </a>

                        <a
                            href="{{ route('results.entry') }}"
                            class="{{ request()->routeIs('results.entry') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('results.entry') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                                <polyline points="14 2 14 8 20 8" />
                                <line x1="16" y1="13" x2="8" y2="13" />
                                <line x1="16" y1="17" x2="8" y2="17" />
                            </svg>
                            Score Entry
                        </a>

                        <a
                            href="{{ route('results.broadsheet') }}"
                            class="{{ request()->routeIs('results.broadsheet') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('results.broadsheet') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16v16H4z" />
                                <path d="M4 9h16" />
                                <path d="M9 4v16" />
                            </svg>
                            Broadsheet
                        </a>

                        <a
                            href="{{ route('attendance') }}"
                            class="{{ request()->routeIs('attendance') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('attendance') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M8 7V3h8v4" />
                                <path d="M5 7h14v14H5z" />
                                <path d="M9 11h6" />
                                <path d="M9 15h6" />
                            </svg>
                            Attendance
                        </a>

                        <a
                            href="{{ route('examination') }}"
                            class="{{ request()->routeIs('examination') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('examination') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M8 2v4" />
                                <path d="M16 2v4" />
                                <path d="M3 10h18" />
                                <path d="M5 6h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z" />
                            </svg>
                            Examination
                        </a>
                    @endif

                    @if ($user?->role === 'admin' || $user?->role === 'bursar')
                        <a
                            href="{{ route('billing.index') }}"
                            class="{{ request()->routeIs('billing.*') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('billing.*') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 1v22" />
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3.5 3.5 0 0 1 0 7H6" />
                            </svg>
                            Billing
                        </a>
                        <a
                            href="{{ route('accounts') }}"
                            class="{{ request()->routeIs('accounts') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('accounts') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M4 4h16v16H4z" />
                                <path d="M8 10h8" />
                                <path d="M8 14h5" />
                            </svg>
                            Accounts
                        </a>
                    @endif

                    <a
                        href="{{ route('more-features') }}"
                        class="{{ request()->routeIs('more-features') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                    >
                        <svg class="{{ request()->routeIs('more-features') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 6v.01" />
                            <path d="M12 12v.01" />
                            <path d="M12 18v.01" />
                            <path d="M7 6v.01" />
                            <path d="M7 12v.01" />
                            <path d="M7 18v.01" />
                            <path d="M17 6v.01" />
                            <path d="M17 12v.01" />
                            <path d="M17 18v.01" />
                        </svg>
                        More Features
                    </a>

                    @if ($user?->role === 'admin')
                        <a
                            href="{{ route('settings') }}"
                            class="{{ request()->routeIs('settings') ? 'bg-brand-50 text-slate-900 ring-1 ring-inset ring-brand-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium"
                        >
                            <svg class="{{ request()->routeIs('settings') ? 'text-slate-700' : 'text-slate-400 group-hover:text-slate-600' }} h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="3" />
                                <path d="M12 1v6M12 17v6M4.22 4.22l4.24 4.24M15.54 15.54l4.24 4.24M1 12h6M17 12h6M4.22 19.78l4.24-4.24M15.54 8.46l4.24-4.24" />
                            </svg>
                            Settings
                        </a>
                    @endif
                </nav>

                <div class="border-t border-gray-200/70 px-6 py-4 text-xs text-gray-500">
                    {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }} &copy; {{ now()->year }}
                </div>
            </aside>

            <div class="lg:pl-64">
                <header class="sticky top-0 z-10 border-b border-gray-200/70 bg-white/90 backdrop-blur">
                    <div class="h-0.5 bg-gradient-to-r from-brand-600 via-brand-500 to-indigo-500"></div>
                    <div class="flex h-16 items-center justify-between px-6">
                        <div class="flex items-center gap-4">
                            <!-- Mobile Menu Button -->
                            <button id="openMobileSidebar" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 lg:hidden">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 12h18M3 6h18M3 18h18" />
                                </svg>
                            </button>
                            <div class="min-w-0">
                                <div class="truncate text-sm font-semibold text-slate-900">{{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ config('myacademy.current_term', 'Term 1') }} · {{ config('myacademy.current_week', 'Week 1') }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Dark Mode Toggle -->
                            <button id="darkModeToggle" class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 transition-colors duration-200">
                                <svg class="h-6 w-6 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="5" />
                                    <line x1="12" y1="1" x2="12" y2="3" />
                                    <line x1="12" y1="21" x2="12" y2="23" />
                                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64" />
                                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78" />
                                    <line x1="1" y1="12" x2="3" y2="12" />
                                    <line x1="21" y1="12" x2="23" y2="12" />
                                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36" />
                                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22" />
                                </svg>
                                <svg class="h-6 w-6 block dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                                </svg>
                            </button>
                            
                            <div class="hidden rounded-xl border border-gray-200/70 bg-white px-3 py-2 text-sm text-slate-600 shadow-sm md:block">
                                {{ now()->format('D, M j, Y') }}
                            </div>
                            <button
                                type="button"
                                class="relative rounded-xl border border-gray-200/70 bg-white p-2 text-slate-500 shadow-sm hover:bg-slate-50"
                                aria-label="Notifications"
                            >
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                                </svg>
                                <span class="absolute right-2 top-2 h-2 w-2 rounded-full bg-orange-500 ring-2 ring-white"></span>
                            </button>
                            <div class="flex items-center gap-2">
                                <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 text-slate-700 ring-1 ring-inset ring-slate-200">
                                    <span class="text-sm font-semibold">
                                        {{ mb_substr($user?->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                                <div class="hidden leading-tight sm:block">
                                    <div class="text-sm font-semibold text-gray-800">{{ $user?->name ?? 'User' }}</div>
                                    <div class="text-xs text-gray-500">{{ ucfirst($user?->role ?? 'user') }}</div>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-200 hover:bg-gray-50"
                                >
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="px-6 py-6">
                    @yield('content')
                    {{ $slot ?? '' }}
                </main>
            </div>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>
