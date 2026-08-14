@props(['title' => 'Dashboard'])

<header class="h-14 bg-white border-b border-gray-200 
               flex items-center justify-between 
               px-4 flex-shrink-0 sticky top-0 z-30">

    {{-- Left Side --}}
    <div class="flex items-center gap-3">
        <span class="text-xl leading-none">🎓</span>
        <div class="flex flex-col">
            <span class="text-[10px] text-gray-400 
                         uppercase tracking-wider font-medium 
                         leading-tight">
                {{ config('app.school_name', config('app.name')) }}
            </span>
            <span class="text-sm font-bold text-gray-800 
                         leading-tight">
                {{ $title }}
            </span>
        </div>
    </div>

    {{-- Right Side --}}
    <div class="flex items-center gap-1">

        {{-- Notification Bell --}}
        <button class="relative p-2 rounded-xl text-gray-400 
                       hover:text-gray-600 hover:bg-gray-100 
                       transition-colors focus:outline-none"
                aria-label="Notifications">
            <i class="ti ti-bell text-lg"></i>
        </button>

        {{-- User Dropdown --}}
        <div class="relative" x-data="{ open: false }">

            <button
                @click="open = !open"
                class="flex items-center gap-2 p-1 
                       rounded-xl hover:bg-gray-50 
                       transition-colors focus:outline-none group"
            >
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatarUrl() }}" 
                         alt="{{ auth()->user()->name }}"
                         class="w-9 h-9 rounded-full object-cover 
                                ring-2 ring-white shadow-md
                                group-hover:ring-green-500 transition-all">
                @else
                    <span class="inline-flex items-center justify-center 
                                 w-9 h-9 rounded-full 
                                 bg-gradient-to-br from-green-600 to-emerald-700 
                                 text-white font-bold text-sm
                                 ring-2 ring-white shadow-md
                                 group-hover:ring-green-500 transition-all">
                        {{ method_exists(auth()->user(), 'initials') 
                            ? auth()->user()->initials() 
                            : strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                @endif
            </button>

            {{-- Dropdown --}}
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
                {{-- User Info (Green Gradient) --}}
                <div class="px-4 py-4 bg-gradient-to-br 
                            from-green-600 to-emerald-700 text-white">
                    <div class="flex items-center gap-3">
                        @if(auth()->user()->avatar)
                            <img src="{{ auth()->user()->avatarUrl() }}" 
                                 alt="{{ auth()->user()->name }}"
                                 class="w-12 h-12 rounded-full object-cover 
                                        ring-2 ring-white/20">
                        @else
                            <span class="inline-flex items-center justify-center 
                                         w-12 h-12 rounded-full 
                                         bg-white/10 text-white font-bold text-lg 
                                         ring-2 ring-white/20">
                                {{ method_exists(auth()->user(), 'initials') 
                                    ? auth()->user()->initials() 
                                    : strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </span>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-bold truncate">
                                {{ auth()->user()->name }}
                            </p>
                            <p class="text-xs text-white/70 truncate">
                                {{ auth()->user()->email }}
                            </p>
                            <span class="inline-flex items-center gap-1 mt-1.5 
                                         px-2 py-0.5 rounded-full 
                                         text-[10px] font-bold 
                                         bg-white/20 text-white">
                                <i class="ti ti-school text-[10px]"></i>
                                គ្រូបង្រៀន
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Assigned Class --}}
                @php
                    $assignedClass = auth()->user()->teacher?->classes()->first();
                @endphp
                @if ($assignedClass)
                    <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                        <p class="text-[10px] text-gray-400 uppercase 
                                  tracking-wider font-medium mb-1">
                            Assigned Class
                        </p>
                        <p class="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
                            <i class="ti ti-building text-green-500 text-sm"></i>
                            {{ $assignedClass->name }}
                            @if ($assignedClass->grade)
                                <span class="text-xs text-gray-400">
                                    ({{ $assignedClass->grade->name }})
                                </span>
                            @endif
                        </p>
                    </div>
                @endif

                {{-- Menu Items --}}
                <div class="py-1">
                    <a href="{{ route('profile.show') }}"
                       class="flex items-center gap-3 px-4 py-2.5 
                              text-sm font-medium text-gray-700 
                              hover:bg-gray-50 transition-colors">
                        <i class="ti ti-user text-gray-400 text-lg"></i>
                        My Profile
                    </a>
                    <a href="{{ route('profile.edit') }}"
                       class="flex items-center gap-3 px-4 py-2.5 
                              text-sm font-medium text-gray-700 
                              hover:bg-gray-50 transition-colors">
                        <i class="ti ti-settings text-gray-400 text-lg"></i>
                        Settings
                    </a>
                </div>

                {{-- Logout --}}
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