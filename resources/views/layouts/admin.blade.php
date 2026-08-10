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
    <script>
    // Prevent sidebar flash on page load
    (function() {
        if (window.innerWidth >= 768 && localStorage.getItem('sidebar_collapsed') === '1') {
            document.documentElement.classList.add('sidebar-preload-collapsed');
        }
    })();
</script>
<style>
    .sidebar-preload-collapsed #sidebar {
        width: 72px !important;
    }
    .sidebar-preload-collapsed #sidebar .sidebar-label,
    .sidebar-preload-collapsed #sidebar .sidebar-section {
        display: none !important;
    }
    .sidebar-preload-collapsed #sidebar nav a,
    .sidebar-preload-collapsed #sidebar nav button {
        justify-content: center !important;
    }
</style>
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
    // Apply state to sidebar based on collapsed status
    function applySidebarState(collapsed) {
    const sidebar = document.getElementById('sidebar');
    const icon = document.getElementById('sidebar-toggle-icon');
    const logoArea = document.getElementById('sidebar-logo-area');

    if (!sidebar) return;

    if (collapsed) {
        sidebar.classList.add('sidebar-collapsed', 'md:w-[72px]');
        logoArea?.classList.remove('justify-between');
        logoArea?.classList.add('justify-center');
        icon?.classList.remove('ti-layout-sidebar-left-collapse');
        icon?.classList.add('ti-layout-sidebar-left-expand');
        sidebar.querySelectorAll('nav a, nav button').forEach(el => {
            el.classList.add('justify-center');
            el.classList.remove('gap-3');
        });
        // Hide text labels
        sidebar.querySelectorAll('.sidebar-label, .sidebar-section').forEach(el => {
            el.style.display = 'none';
        });
    } else {
        sidebar.classList.remove('sidebar-collapsed', 'md:w-[72px]');
        logoArea?.classList.remove('justify-center');
        logoArea?.classList.add('justify-between');
        icon?.classList.remove('ti-layout-sidebar-left-expand');
        icon?.classList.add('ti-layout-sidebar-left-collapse');
        sidebar.querySelectorAll('nav a, nav button').forEach(el => {
            el.classList.remove('justify-center');
            el.classList.add('gap-3');
        });
        // Show text labels
        sidebar.querySelectorAll('.sidebar-label, .sidebar-section').forEach(el => {
            el.style.display = '';
        });
    }
}

    // Toggle sidebar and save state
    function toggleSidebar() {
        if (window.innerWidth < 768) return;
        
        const sidebar = document.getElementById('sidebar');
        const isCurrentlyCollapsed = sidebar.classList.contains('sidebar-collapsed');
        const newState = !isCurrentlyCollapsed;
        
        applySidebarState(newState);
        localStorage.setItem('sidebar_collapsed', newState ? '1' : '0');
    }

    function expandIfCollapsed() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar?.classList.contains('sidebar-collapsed')) {
            applySidebarState(false);
            localStorage.setItem('sidebar_collapsed', '0');
        }
    }

    // Restore sidebar state on page load
    document.addEventListener('DOMContentLoaded', function () {
    if (window.innerWidth >= 768) {
        const saved = localStorage.getItem('sidebar_collapsed');
        if (saved === '1') {
            applySidebarState(true);
        }
        // Remove preload class after JS applies state
        document.documentElement.classList.remove('sidebar-preload-collapsed');
    }
});
</script>
</body>
</html>