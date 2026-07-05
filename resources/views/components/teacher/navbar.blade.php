@props(['title' => 'Dashboard'])
<header class="h-16 flex items-center justify-between px-4 md:px-6 flex-shrink-0
               sticky top-0 z-20
               m-3 md:mb-3 md:mt-3 md:mx-3
               rounded-2xl
               bg-white/35 backdrop-blur-2xl backdrop-saturate-150
               border border-white/50
               shadow-[0_1px_0_rgba(255,255,255,0.6)_inset,0_8px_24px_rgba(15,23,42,0.06)]
               transition-colors">
    <div class="flex items-center gap-4">
        {{-- Mobile only hamburger (desktop uses sidebar's own toggle) --}}
        <button
            class="md:hidden text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-xl
                   bg-white/40 hover:bg-white/70 border border-white/60 backdrop-blur-sm
                   transition-all active:scale-95"
            onclick="toggleSidebar()"
            aria-label="Toggle sidebar"
        >
            <i class="ti ti-menu-2 text-xl"></i>
        </button>
        {{-- School name + page title --}}
        <div class="flex flex-col">
            <span class="text-sm font-bold text-gray-800 leading-tight">{{ config('app.school_name') }}</span>
            <span class="text-xs text-gray-500 leading-tight">{{ $title }}</span>
        </div>
    </div>
    {{-- Right side --}}
    <div class="flex items-center gap-3">
        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                class="flex items-center gap-2 text-sm text-gray-700
                       hover:text-gray-900 focus:outline-none
                       pl-2 pr-1 py-1 rounded-full
                       bg-white/40 hover:bg-white/70 border border-white/60 backdrop-blur-sm
                       transition-all active:scale-95"
            >
                <span class="hidden sm:inline font-medium">
                    {{ auth()->user()->name }}
                </span>
                <span class="inline-flex items-center justify-center w-8 h-8
                             rounded-full text-white
                             font-bold text-sm
                             bg-gradient-to-br from-green-500 to-green-700
                             shadow-[0_2px_6px_rgba(22,163,74,0.35)]">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            </button>
            <div
                x-show="open"
                x-cloak
                @click.outside="open = false"
                x-transition
                class="absolute right-0 mt-2 w-48
                       bg-white/70 backdrop-blur-xl backdrop-saturate-150
                       border border-white/60
                       rounded-2xl shadow-[0_10px_40px_rgba(15,23,42,0.15)] z-50
                       overflow-hidden"
            >
                <div class="px-4 py-3 border-b border-white/50">
                    <p class="text-xs font-semibold text-gray-800">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500">Teacher</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full text-left px-4 py-2.5 text-sm
                               text-red-600 hover:bg-red-50/60 transition-colors
                               flex items-center gap-2"
                    >
                        <i class="ti ti-logout text-base"></i>
                        Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const icon = document.getElementById('sidebar-toggle-icon');
        const logoArea = document.getElementById('sidebar-logo-area');
        const isMobile = window.innerWidth < 768;

        if (isMobile) {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        } else {
            const isCollapsing = sidebar.classList.contains('w-64');
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('w-16');
            sidebar.querySelectorAll('.sidebar-label').forEach(el => el.classList.toggle('hidden'));

            if (isCollapsing) {
                logoArea.classList.remove('justify-between');
                logoArea.classList.add('justify-center');
                icon.classList.replace('ti-layout-sidebar-left-collapse', 'ti-layout-sidebar-left-expand');
            } else {
                logoArea.classList.remove('justify-center');
                logoArea.classList.add('justify-between');
                icon.classList.replace('ti-layout-sidebar-left-expand', 'ti-layout-sidebar-left-collapse');
            }
        }
    }
</script>
