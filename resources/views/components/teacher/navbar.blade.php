@props(['title' => 'Dashboard'])
<header class="h-16 bg-white border-b border-gray-200 flex items-center
               justify-between px-4 md:px-6 flex-shrink-0">
    <div class="flex items-center gap-4">
        {{-- Sidebar toggle — visible on all screen sizes --}}
        <button
            class="text-gray-500 hover:text-gray-700 focus:outline-none p-1.5 rounded-lg hover:bg-gray-100 transition-colors"
            onclick="toggleSidebar()"
            aria-label="Toggle sidebar"
        >
            <i class="ti ti-menu-2 text-xl"></i>
        </button>
        <h1 class="text-lg font-semibold text-gray-800">{{ $title }}</h1>
    </div>
    {{-- Right side --}}
    <div class="flex items-center gap-4">
        <div class="relative" x-data="{ open: false }">
            <button
                @click="open = !open"
                class="flex items-center gap-2 text-sm text-gray-700
                       hover:text-gray-900 focus:outline-none"
            >
                <span class="hidden sm:inline font-medium">
                    {{ auth()->user()->name }}
                </span>
                <span class="inline-flex items-center justify-center w-8 h-8
                             rounded-full bg-gray-200 text-gray-600
                             font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            </button>
            <div
                x-show="open"
                @click.outside="open = false"
                x-transition
                class="absolute right-0 mt-2 w-44 bg-white border
                       border-gray-200 rounded-md shadow-lg z-50"
            >
                <div class="px-4 py-2 border-b border-gray-100">
                    <p class="text-xs font-semibold text-gray-700">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-400">Teacher</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="w-full text-left px-4 py-2 text-sm
                               text-red-600 hover:bg-gray-50"
                    >
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
        const isMobile = window.innerWidth < 768;

        if (isMobile) {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        } else {
            // Toggle the inline margin directly — default state is closed (-16rem)
            const isOpen = sidebar.style.marginLeft === '0rem' || sidebar.style.marginLeft === '0px';
            sidebar.style.marginLeft = isOpen ? '-16rem' : '0rem';
        }
    }
</script>
