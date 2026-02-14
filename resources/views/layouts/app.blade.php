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
        @php
            /** @var \App\Support\LicenseManager $licenses */
            $licenses = app(\App\Support\LicenseManager::class);
            $licenseState = $licenses->load();
            $licenseOk = (bool) ($licenseState['ok'] ?? false);
            $licenseFeatures = $licenseOk ? ($licenseState['data']['features'] ?? []) : [];
            if (! is_array($licenseFeatures)) {
                $licenseFeatures = [];
            }

            $hasCbt = $licenseOk && in_array('cbt', $licenseFeatures, true);
            $hasSavingsLoan = $licenseOk && in_array('savings_loan', $licenseFeatures, true);

            $appMode = (string) config('myacademy.mode', 'full');
            $premiumEnforce = (bool) config('myacademy.premium_enforce', true);
            $cbtLocked = $premiumEnforce && ! $hasCbt;
            $showSavingsLoan = $premiumEnforce ? $hasSavingsLoan : true;
        @endphp

        <div id="app" class="min-h-screen">
            <!-- Mobile Sidebar Overlay -->
            <div id="mobileSidebarOverlay" class="fixed inset-0 z-40 hidden bg-black/50 lg:hidden"></div>
            
            <!-- Mobile Sidebar -->
            <aside id="mobileSidebar" class="fixed inset-y-0 left-0 z-50 w-80 transform -translate-x-full transition-transform duration-300 lg:hidden">
                <div class="flex h-full flex-col bg-white dark:bg-dark-100">
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-dark-200 px-4 py-4">
                        <div class="flex items-center gap-3">
                            @php($schoolLogo = config('myacademy.school_logo'))
                            <div class="grid h-10 w-10 place-items-center overflow-hidden rounded-xl bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-100">
                                @if ($schoolLogo)
                                    <img
                                        src="{{ asset('uploads/'.str_replace('\\', '/', $schoolLogo)) }}"
                                        alt="School logo"
                                        class="h-full w-full bg-white object-contain p-1.5"
                                    />
                                @else
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 3 1 9l11 6 9-4.91V17a2 2 0 0 1-1.1 1.79l-7.4 3.7a2 2 0 0 1-1.8 0l-7.4-3.7A2 2 0 0 1 2 17V9" />
                                        <path d="M12 21V9" />
                                    </svg>
                                @endif
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
                                
                                @php($cbtHref = $cbtLocked ? ($user?->role === 'admin' ? route('marketplace') : route('more-features')) : route('cbt.index'))
                                <a href="{{ $cbtHref }}" class="card-interactive p-4 text-center {{ $cbtLocked ? 'opacity-60' : '' }}">
                                    <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-violet-50 text-violet-600">
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="3" y="4" width="18" height="12" rx="2" />
                                            <path d="M8 20h8" />
                                            <path d="M10 10l2 2 4-4" />
                                        </svg>
                                    </div>
                                    <div class="text-xs font-semibold text-gray-700">CBT</div>
                                    @if ($cbtLocked)
                                        <div class="mt-1 text-[10px] font-black uppercase tracking-wider text-orange-700">Locked</div>
                                    @endif
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

                                @if ($showSavingsLoan)
                                    <a href="{{ route('savings-loan.index') }}" class="card-interactive p-4 text-center">
                                        <div class="mx-auto mb-2 grid h-12 w-12 place-items-center rounded-lg bg-emerald-50 text-emerald-600">
                                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 7H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z" />
                                                <path d="M16 17v2a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2" />
                                                <path d="M6 11h.01M10 11h.01" />
                                            </svg>
                                        </div>
                                        <div class="text-xs font-semibold text-gray-700">Savings/Loan</div>
                                    </a>
                                @endif
                            @endif
                            
                            @if (in_array($user?->role, ['admin', 'teacher', 'bursar'], true))
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
            <aside id="desktopSidebar" class="fixed inset-y-0 left-0 hidden w-64 flex-col bg-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] transition-all duration-300 lg:flex">
                <div class="flex items-center justify-between border-b border-gray-200 bg-gradient-to-br from-slate-50 to-gray-100 px-6 py-5 shadow-sm">
                    <div class="flex items-center gap-3 min-w-0">
                    @php($schoolLogo = config('myacademy.school_logo'))
                    <div class="icon-3d grid h-11 w-11 place-items-center overflow-hidden rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg ring-1 ring-gray-200">
                        @if ($schoolLogo)
                            <img
                                src="{{ asset('uploads/'.str_replace('\\', '/', $schoolLogo)) }}"
                                alt="School logo"
                                class="h-full w-full object-contain p-1.5 bg-white rounded-lg"
                            />
                        @else
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 3 1 9l11 6 9-4.91V17a2 2 0 0 1-1.1 1.79l-7.4 3.7a2 2 0 0 1-1.8 0l-7.4-3.7A2 2 0 0 1 2 17V9" />
                                <path d="M12 21V9" />
                            </svg>
                        @endif
                    </div>
                        <div class="min-w-0">
                            <div class="sidebar-text truncate text-base font-black tracking-tight text-gray-900">
                                {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}
                            </div>
                            <div class="sidebar-text mt-0.5 text-xs font-semibold text-gray-600">
                                {{ config('myacademy.current_term', 'Term 1') }} · {{ config('myacademy.current_week', 'Week 1') }}
                            </div>
                        </div>
                    </div>
                    <button id="sidebarToggle" class="rounded-lg p-1.5 text-gray-600 hover:bg-gray-200 transition-colors">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M15 18l-6-6 6-6"/>
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-4">
                    @php($user = auth()->user())

                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                        <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? '' : 'text-indigo-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="3" width="7" height="7"/>
                            <rect x="14" y="3" width="7" height="7"/>
                            <rect x="14" y="14" width="7" height="7"/>
                            <rect x="3" y="14" width="7" height="7"/>
                        </svg>
                        <span class="sidebar-text">Dashboard</span>
                    </a>

                    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.*') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                        <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('students.*') ? '' : 'text-blue-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span class="sidebar-text">Students</span>
                    </a>

                    @if ($user?->role === 'admin' || $user?->role === 'teacher')
                        <a href="{{ route('teachers') }}" class="{{ request()->routeIs('teachers') || request()->routeIs('teachers.*') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('teachers') || request()->routeIs('teachers.*') ? '' : 'text-orange-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                            <span class="sidebar-text">Teachers</span>
                        </a>

                        <a href="{{ route('classes.index') }}" class="{{ request()->routeIs('classes.*') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('classes.*') ? '' : 'text-purple-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="3" y1="9" x2="21" y2="9"/>
                            </svg>
                            <span class="sidebar-text">Classes</span>
                        </a>

                        <a href="{{ route('subjects.index') }}" class="{{ request()->routeIs('subjects.*') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('subjects.*') ? '' : 'text-cyan-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                            <span class="sidebar-text">Subjects</span>
                        </a>

                        <a href="{{ route('results.entry') }}" class="{{ request()->routeIs('results.entry') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('results.entry') ? '' : 'text-green-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            <span class="sidebar-text">Score Entry</span>
                        </a>

                        <a href="{{ route('results.broadsheet') }}" class="{{ request()->routeIs('results.broadsheet') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('results.broadsheet') ? '' : 'text-emerald-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                                <polyline points="10 9 9 9 8 9"/>
                            </svg>
                            <span class="sidebar-text">Broadsheet</span>
                        </a>

                        <a href="{{ route('attendance') }}" class="{{ request()->routeIs('attendance') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('attendance') ? '' : 'text-teal-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <polyline points="16 11 18 13 22 9"/>
                            </svg>
                            <span class="sidebar-text">Attendance</span>
                        </a>

                        <a href="{{ route('messages') }}" class="{{ request()->routeIs('messages') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('messages') ? '' : 'text-pink-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                            <span class="sidebar-text">Messages</span>
                            <span class="ml-auto flex items-center">
                                <livewire:messages.unread-badge />
                            </span>
                        </a>

                    @php($cbtHref = $cbtLocked ? ($user?->role === 'admin' ? route('marketplace') : route('more-features')) : route('cbt.index'))
                    @php($cbtIsActive = ! $cbtLocked && request()->routeIs('cbt.*'))
                    <a href="{{ $cbtHref }}" class="{{ $cbtIsActive ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} {{ $cbtLocked ? 'opacity-60' : '' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                        <svg class="h-5 w-5 flex-shrink-0 {{ $cbtIsActive ? '' : 'text-violet-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="4" width="18" height="12" rx="2" ry="2" />
                            <path d="M8 20h8" />
                            <path d="M10 10l2 2 4-4" />
                        </svg>
                        <span class="sidebar-text">CBT</span>
                        @if ($cbtLocked)
                            <span class="ml-auto rounded-full bg-orange-100 px-2 py-1 text-[10px] font-black uppercase tracking-wider text-orange-800">Locked</span>
                        @endif
                    </a>
                    @endif

                    @if ($user?->role === 'admin' || $user?->role === 'bursar')
                        <a href="{{ route('billing.index') }}" class="{{ request()->routeIs('billing.*') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('billing.*') ? '' : 'text-amber-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                                <line x1="1" y1="10" x2="23" y2="10"/>
                            </svg>
                            <span class="sidebar-text">Billing</span>
                        </a>
                        <a href="{{ route('accounts') }}" class="{{ request()->routeIs('accounts') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('accounts') ? '' : 'text-yellow-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="12" y1="1" x2="12" y2="23"/>
                                <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                            <span class="sidebar-text">Accounts</span>
                        </a>

                        @if ($showSavingsLoan)
                            <a href="{{ route('savings-loan.index') }}" class="{{ request()->routeIs('savings-loan.*') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                                <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('savings-loan.*') ? '' : 'text-emerald-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <rect x="3" y="7" width="18" height="10" rx="2" ry="2" />
                                    <path d="M16 17v2a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2v-2" />
                                    <path d="M6 12h.01M10 12h.01" />
                                </svg>
                                <span class="sidebar-text">Savings/Loan</span>
                            </a>
                        @endif
                    @endif

                    @if (in_array($user?->role, ['admin', 'teacher', 'bursar'], true))
                        <a href="{{ route('more-features') }}" class="{{ request()->routeIs('more-features') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('more-features') ? '' : 'text-slate-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="1"/>
                                <circle cx="19" cy="12" r="1"/>
                                <circle cx="5" cy="12" r="1"/>
                            </svg>
                            <span class="sidebar-text">More Features</span>
                        </a>
                    @endif

                    @if ($user?->role === 'admin')
                        <a href="{{ route('settings') }}" class="{{ request()->routeIs('settings') ? 'bg-gradient-to-br from-slate-700 to-slate-900 text-white shadow-lg shadow-slate-300' : 'text-gray-700 hover:bg-gradient-to-br hover:from-gray-50 hover:to-gray-100 hover:shadow-md' }} mb-1 group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-bold transition-all">
                            <svg class="h-5 w-5 flex-shrink-0 {{ request()->routeIs('settings') ? '' : 'text-gray-500' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="3"/>
                                <path d="M12 1v6m0 6v6M5.64 5.64l4.24 4.24m4.24 4.24l4.24 4.24M1 12h6m6 0h6M5.64 18.36l4.24-4.24m4.24-4.24l4.24-4.24"/>
                            </svg>
                            <span class="sidebar-text">Settings</span>
                        </a>
                    @endif
                </nav>

            </aside>

            <div id="mainContent" class="lg:pl-64 transition-all duration-300">
                <header class="sticky top-0 z-10 border-b border-gray-200/70 bg-gradient-to-r from-slate-50 via-white to-slate-50 backdrop-blur-xl shadow-md">
                    <div class="h-1.5 bg-gradient-to-r from-slate-700 via-blue-600 to-slate-700"></div>
                    <div class="flex h-16 items-center justify-between px-6">
                        <div class="flex items-center gap-4">
                            <!-- Mobile Menu Button -->
                            <button id="openMobileSidebar" class="rounded-xl p-2.5 text-gray-500 hover:bg-white hover:shadow-md transition-all lg:hidden">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M3 12h18M3 6h18M3 18h18" />
                                </svg>
                            </button>
                            <div class="min-w-0">
                                <div class="truncate text-sm font-black text-slate-900">{{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}</div>
                                <div class="text-xs font-semibold text-slate-600">
                                    {{ now()->format('l, F j, Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Dark Mode Toggle -->
                            <button id="darkModeToggle" class="rounded-xl p-2.5 text-gray-500 hover:bg-white hover:shadow-md transition-all duration-200">
                                <svg class="h-5 w-5 hidden dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
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
                                <svg class="h-5 w-5 block dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z" />
                                </svg>
                            </button>
                            
                            <livewire:notifications.bell />
                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 rounded-xl bg-white p-1.5 shadow-sm ring-1 ring-gray-200 hover:shadow-md transition-all">
                                @if ($user?->profile_photo_url)
                                    <img
                                        src="{{ $user->profile_photo_url }}"
                                        alt="{{ $user->name }}"
                                        class="h-9 w-9 rounded-lg object-cover ring-2 ring-slate-200"
                                    />
                                @else
                                    <div class="grid h-9 w-9 place-items-center rounded-lg bg-gradient-to-br from-slate-700 to-slate-900 text-white ring-2 ring-slate-200">
                                        <span class="text-sm font-bold">
                                            {{ mb_substr($user?->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                                <div class="hidden pr-2 leading-tight sm:block">
                                    <div class="text-sm font-bold text-gray-900">{{ $user?->name ?? 'User' }}</div>
                                    <div class="text-xs font-semibold text-gray-500">{{ ucfirst($user?->role ?? 'user') }}</div>
                                </div>
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-br from-slate-700 to-slate-900 px-4 py-2.5 text-sm font-bold text-white shadow-md hover:shadow-lg transition-all"
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
        
        <script>
            const sidebar = document.getElementById('desktopSidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('sidebarToggle');
            const sidebarTexts = document.querySelectorAll('.sidebar-text');
            let isCollapsed = false;

            toggleBtn.addEventListener('click', () => {
                isCollapsed = !isCollapsed;
                
                if (isCollapsed) {
                    sidebar.classList.remove('w-64');
                    sidebar.classList.add('w-20');
                    mainContent.classList.remove('lg:pl-64');
                    mainContent.classList.add('lg:pl-20');
                    sidebarTexts.forEach(text => text.classList.add('hidden'));
                    toggleBtn.querySelector('svg').style.transform = 'rotate(180deg)';
                } else {
                    sidebar.classList.remove('w-20');
                    sidebar.classList.add('w-64');
                    mainContent.classList.remove('lg:pl-20');
                    mainContent.classList.add('lg:pl-64');
                    sidebarTexts.forEach(text => text.classList.remove('hidden'));
                    toggleBtn.querySelector('svg').style.transform = 'rotate(0deg)';
                }
            });
        </script>
    </body>
</html>
