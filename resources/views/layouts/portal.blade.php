<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }} &middot; CBT Portal</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-50 text-slate-900">
        <div class="mx-auto w-full max-w-5xl px-4 py-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    @php($schoolLogo = config('myacademy.school_logo'))
                    <div class="grid h-11 w-11 place-items-center overflow-hidden rounded-xl bg-white ring-1 ring-inset ring-slate-200 shadow-sm">
                        @if ($schoolLogo)
                            <img
                                src="{{ asset('uploads/'.str_replace('\\', '/', $schoolLogo)) }}"
                                alt="School logo"
                                class="h-full w-full object-contain p-1.5"
                            />
                        @else
                            <svg class="h-6 w-6 text-slate-700" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 3 1 9l11 6 9-4.91V17a2 2 0 0 1-1.1 1.79l-7.4 3.7a2 2 0 0 1-1.8 0l-7.4-3.7A2 2 0 0 1 2 17V9" />
                                <path d="M12 21V9" />
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <div class="truncate text-sm font-black tracking-tight text-slate-900">
                            {{ config('myacademy.school_name', config('app.name', 'MyAcademy')) }}
                        </div>
                        <div class="text-xs font-semibold text-slate-600">CBT Portal</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="btn-outline">Staff Login</a>
                </div>
            </div>

            <main class="mt-6">
                {{ $slot }}
            </main>
        </div>

        @livewireScripts
        @stack('scripts')
    </body>
</html>

