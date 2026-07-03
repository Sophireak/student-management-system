<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name') }} – {{ $title ?? 'Dashboard' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    {{-- Dark mode: apply saved preference before first paint (no flash) --}}
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    {{-- Prevent sidebar transition flicker on page load --}}
    <style>
        #sidebar { transition: none !important; }
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Re-enable transitions after first paint --}}
    <script>
        window.addEventListener('load', () => {
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    const style = document.createElement('style');
                    style.textContent = '#sidebar { transition: none !important; display: none; }';
                    const sidebar = document.getElementById('sidebar');
                    if (sidebar) sidebar.style.transition = '';
                });
            });
        });
    </script>
</head>
<body class="{{ auth()->user()->isAdmin() ? 'bg-gray-50 dark:bg-gradient-to-br dark:from-gray-800 dark:via-gray-700 dark:to-gray-900' : 'bg-gray-50 dark:bg-gradient-to-br dark:from-gray-800 dark:via-gray-700 dark:to-gray-900' }} font-sans antialiased transition-colors relative">
    @unless (auth()->user()->isAdmin())
        {{-- Khmer-toned ambient background (same as dashboard, now global for all teacher pages) --}}
        <div class="fixed inset-0 -z-10 bg-gradient-to-br from-green-50 via-amber-50/40 to-yellow-50 dark:hidden pointer-events-none"></div>
    @endunless
    <div class="flex h-screen overflow-hidden">
        @if (auth()->user()->isAdmin())
            <x-admin.sidebar />
        @else
            <x-teacher.sidebar />
        @endif
        <div class="flex flex-col flex-1 overflow-hidden">
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

    {{-- Global dark mode toggle function --}}
    <script>
        function toggleDarkMode() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        }
    </script>
</body>
</html>
