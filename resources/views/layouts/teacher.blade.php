<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" 
          content="width=device-width, initial-scale=1, 
                   viewport-fit=cover, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" 
          content="default">

    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    [x-cloak] {
        display: none !important;
    }

    /* Safe area for bottom nav (iPhone notch) */
    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom, 0px);
    }

    /* Prevent content hiding behind bottom nav */
    .mb-bottom-nav {
        margin-bottom: 5rem;
    }
</style>

    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased">

    <div class="flex flex-col min-h-screen">

        {{-- Navbar --}}
        <x-teacher.navbar :title="$title ?? 'Dashboard'" />

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 mb-bottom-nav">
            

            {{-- Main Content --}}
            @yield('content')

        </main>

        {{-- Bottom Navigation --}}
        @include('components.teacher.bottom-nav')

    </div>
 {{-- Toast Notifications --}}
    <x-flash-alert />
    @stack('scripts')

</body>
</html>