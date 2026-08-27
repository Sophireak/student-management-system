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
    /* Prevent content hiding behind floating bottom nav */
    .mb-bottom-nav {
        margin-bottom: 6.5rem;
    }
    /* Ambient gradient blobs */
    .bg-blob {
        position: fixed;
        border-radius: 9999px;
        filter: blur(60px);
        z-index: 0;
        pointer-events: none;
    }
</style>
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-800 bg-gradient-to-br from-green-100 via-white to-amber-100">
    {{-- Ambient background blobs --}}
    <div class="bg-blob w-72 h-72 bg-green-300/30 -top-16 -left-16"></div>
    <div class="bg-blob w-80 h-80 bg-amber-300/30 top-1/3 -right-20"></div>
    <div class="bg-blob w-64 h-64 bg-green-200/25 bottom-0 left-1/4"></div>
    <div class="relative z-10 flex flex-col min-h-screen">
        {{-- Navbar --}}
        <x-teacher.navbar :title="$title ?? 'Dashboard'" />
        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 mb-bottom-nav">
            {{-- Main Content --}}
            <div class="max-w-4xl mx-auto w-full">
                @yield('content')
            </div>
        </main>
        {{-- Bottom Navigation --}}
        @include('components.teacher.bottom-nav')
    </div>
    {{-- Toast Notifications --}}
    <x-flash-alert />
    @stack('scripts')
</body>
</html>
