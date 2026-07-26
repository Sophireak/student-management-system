@props(['title' => 'Dashboard'])

<header class="h-16 bg-white border-b border-gray-200 
               flex items-center justify-between 
               px-4 md:px-6 flex-shrink-0 sticky top-0 z-20">

    {{-- Left Side --}}
    <div class="flex items-center gap-3">

        {{-- Mobile: App Name --}}
        <div class="md:hidden flex items-center gap-2">
            <span class="text-lg leading-none">🎓</span>
            <span class="text-sm font-extrabold text-gray-800 tracking-tight">
                {{ config('app.name') }}
            </span>
        </div>

        {{-- Desktop: Title --}}
        <div class="hidden md:flex flex-col">
            <span class="text-[10px] text-gray-400 
                         uppercase tracking-wider font-medium leading-tight">
                {{ config('app.school_name', config('app.name')) }}
            </span>
            <span class="text-base font-bold text-gray-800 leading-tight">
                {{ $title }}
            </span>
        </div>
    </div>

    {{-- Right Side --}}
    <div class="flex items-center gap-1">

        {{-- Notification Bell --}}
        <button class="relative p-2.5 rounded-xl text-gray-400 
                       hover:text-gray-600 hover:bg-gray-100 
                       transition-colors focus:outline-none"
                aria-label="Notifications">
            <i class="ti ti-bell text-xl"></i>
        </button>

        {{-- Divider (desktop only) --}}
        <div class="hidden sm:block w-px h-8 bg-gray-200 mx-2"></div>

        {{-- MOBILE: Avatar opens slide panel --}}
        <button 
            @click="openMenu()"
            class="md:hidden relative"
        >
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatarUrl() }}" 
                     alt="{{ auth()->user()->name }}"
                     class="w-10 h-10 rounded-full object-cover 
                            ring-2 ring-white shadow-md">
            @else
                <span class="inline-flex items-center justify-center 
                             w-10 h-10 rounded-full 
                             bg-gradient-to-br from-slate-700 to-slate-900 
                             text-white font-bold text-sm
                             ring-2 ring-white shadow-md">
                    {{ auth()->user()->initials() }}
                </span>
            @endif
        </button>

        {{-- DESKTOP: Avatar opens dropdown --}}
        <div class="hidden md:block relative" x-data="{ open: false }">

            <button
                @click="open = !open"
                class="flex items-center gap-3 pr-1 rounded-full 
                       hover:bg-gray-50 transition-colors 
                       focus:outline-none group"
            >
                <div class="flex flex-col items-end">
                    <span class="text-base font-bold text-gray-800 leading-tight">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="inline-flex items-center gap-1 mt-1 
                                 px-2.5 py-0.5 rounded-full 
                                 text-[10px] font-bold 
                                 bg-slate-800 text-white -mr-2">
                        <i class="ti ti-{{ auth()->user()->isAdmin() 
                                    ? 'shield-check' : 'school' }} text-[10px]">
                        </i>
                        {{ auth()->user()->isAdmin() ? 'Administrator' : 'គ្រូបង្រៀន' }}
                    </span>
                </div>

                <div class="relative">
                    @if(auth()->user()->avatar)
                        <img src="{{ auth()->user()->avatarUrl() }}" 
                             alt="{{ auth()->user()->name }}"
                             class="w-11 h-11 rounded-full object-cover 
                                    ring-2 ring-white shadow-md
                                    group-hover:ring-slate-800 transition-all">
                    @else
                        <span class="inline-flex items-center justify-center 
                                     w-11 h-11 rounded-full 
                                     bg-gradient-to-br from-slate-700 to-slate-900 
                                     text-white font-bold text-sm
                                     ring-2 ring-white shadow-md
                                     group-hover:ring-slate-800 transition-all">
                            {{ auth()->user()->initials() }}
                        </span>
                    @endif
                </div>
            </button>

            {{-- Desktop Dropdown (small, account only) --}}
            <div
                x-show="open"
                x-cloak
                @click.outside="open = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-3 w-64 bg-white 
                       border border-gray-200 rounded-2xl 
                       shadow-xl z-50 overflow-hidden"
            >
                {{-- User Info --}}
                <div class="px-4 py-4 bg-gradient-to-br 
                            from-slate-800 to-slate-900 text-white">
                    <div class="flex items-center gap-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatarUrl() }}" 
                                 class="w-12 h-12 rounded-full object-cover 
                                        ring-2 ring-white/20">
                        @else
                            <span class="inline-flex items-center justify-center 
                                         w-12 h-12 rounded-full 
                                         bg-white/10 text-white font-bold text-lg 
                                         ring-2 ring-white/20">
                                {{ auth()->user()->initials() }}
                            </span>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm font-bold truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-white/60 truncate">
                                {{ auth()->user()->email }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="py-1">
                    <a href="{{ route('profile.show') }}"
                       class="flex items-center gap-3 px-4 py-2.5 
                              text-sm font-medium text-gray-700 
                              hover:bg-gray-50 transition-colors">
                        <i class="ti ti-user text-gray-400 text-lg"></i>
                        My Profile
                    </a>
                    <a href="#"
                       class="flex items-center gap-3 px-4 py-2.5 
                              text-sm font-medium text-gray-700 
                              hover:bg-gray-50 transition-colors">
                        <i class="ti ti-settings text-gray-400 text-lg"></i>
                        Settings
                    </a>
                </div>

                <div class="border-t border-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full flex items-center gap-3 
                                       px-4 py-3 text-sm font-semibold 
                                       text-red-600 hover:bg-red-50 
                                       transition-colors">
                            <i class="ti ti-logout text-red-500 text-lg"></i>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>