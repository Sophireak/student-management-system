<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name') }}</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased">

<div 
    x-data="{
        mobileMenu: false,
        openMenu()  { this.mobileMenu = true; document.body.classList.add('overflow-hidden') },
        closeMenu() { this.mobileMenu = false; document.body.classList.remove('overflow-hidden') }
    }"
    class="flex h-screen overflow-hidden"
>
    {{-- Mobile Menu Overlay --}}
    <div
        x-show="mobileMenu"
        x-cloak
        @click="closeMenu()"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 md:hidden"
    ></div>

    {{-- Mobile Slide Panel --}}
    @include('components.admin.mobile-menu')

    {{-- Desktop Sidebar --}}
    @include('components.admin.sidebar')

    {{-- Main Content Area --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Navbar --}}
        <x-admin.navbar :title="$title ?? 'Dashboard'" />

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-6">

            

            @yield('content')

        </main>
    </div>

</div>
{{-- Toast Notifications --}}
<x-flash-alert />
@stack('scripts')

{{-- Desktop Sidebar Toggle --}}
<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const icon = document.getElementById('sidebar-toggle-icon');
        const logoArea = document.getElementById('sidebar-logo-area');

        if (window.innerWidth < 768) return;

        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
        sidebar.classList.toggle('sidebar-collapsed');

        if (isCollapsed) {
            sidebar.classList.remove('md:w-[72px]');
            logoArea.classList.remove('justify-center');
            logoArea.classList.add('justify-between');
            icon.classList.replace('ti-layout-sidebar-left-expand', 'ti-layout-sidebar-left-collapse');
            sidebar.querySelectorAll('nav a, nav button').forEach(el => {
                el.classList.remove('justify-center');
                el.classList.add('gap-3');
            });
        } else {
            sidebar.classList.add('md:w-[72px]');
            logoArea.classList.replace('justify-between', 'justify-center');
            icon.classList.replace('ti-layout-sidebar-left-collapse', 'ti-layout-sidebar-left-expand');
            sidebar.querySelectorAll('nav a, nav button').forEach(el => {
                el.classList.add('justify-center');
                el.classList.remove('gap-3');
            });
        }
    }

    function expandIfCollapsed() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar.classList.contains('sidebar-collapsed')) {
            toggleSidebar();
        }
    }
</script>
</body>
</html>