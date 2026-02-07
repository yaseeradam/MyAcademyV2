<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name', 'MyAcademy') }}</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="h-full bg-gray-50 text-gray-900">
        <main class="flex min-h-screen items-center justify-center px-6 py-10">
            {{ $slot }}
        </main>
    </body>
</html>
