<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name') }} — {{ $title ?? 'Dashboard' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar — role-aware --}}
        @if (auth()->user()->isAdmin())
            <x-admin.sidebar />
        @else
            <x-teacher.sidebar />
        @endif

        <div class="flex flex-col flex-1 overflow-hidden">

            {{-- Navbar — role-aware --}}
            @if (auth()->user()->isAdmin())
                <x-admin.navbar :title="$title ?? 'Dashboard'" />
            @else
                <x-teacher.navbar :title="$title ?? 'Dashboard'" />
            @endif

            <main class="flex-1 overflow-y-auto p-6">
                <x-admin.alert />
                @yield('content')
            </main>

        </div>
    </div>
    @stack('scripts')
</body>
</html>