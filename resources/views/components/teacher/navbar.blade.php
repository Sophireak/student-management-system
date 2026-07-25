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
                {{ config('app.school_name') }}
            </span>
            <span class="text-sm font-bold text-gray-800 
                         leading-tight">
                {{ $title }}
            </span>
        </div>
    </div>

    {{-- Right Side --}}
    <div class="flex items-center gap-2">

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
                       rounded-xl hover:bg-gray-100 
                       transition-colors focus:outline-none"
            >
                <span class="inline-flex items-center justify-center 
                             w-8 h-8 rounded-xl bg-green-100 
                             text-green-700 font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </span>
            </button>

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
                class="absolute right-0 mt-2 w-56 bg-white 
                       border border-gray-200 rounded-xl 
                       shadow-lg z-50 overflow-hidden"
            >
                {{-- User Info --}}
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-100">
                    <p class="text-sm font-semibold text-gray-800">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ auth()->user()->email }}
                    </p>
                    <span class="inline-block mt-1.5 px-2 py-0.5 
                                 text-[10px] font-semibold uppercase 
                                 tracking-wide rounded-full
                                 bg-green-100 text-green-700">
                        Teacher
                    </span>
                </div>

                {{-- Class Info --}}
                <div class="px-4 py-2.5 border-b border-gray-100">
                    <p class="text-[10px] text-gray-400 uppercase 
                              tracking-wider font-medium">
                        Assigned Class
                    </p>
                    <p class="text-sm font-semibold text-gray-700 mt-0.5">
                        {{ auth()->user()->assignedClass->name ?? 'No Class' }}
                    </p>
                </div>

                {{-- Menu Items --}}
                <div class="py-1.5">
                    <a href="{{ route('profile.show') }}"
                       class="flex items-center gap-3 px-4 py-2.5 
                              text-sm text-gray-700 
                              hover:bg-gray-50 transition-colors">
                        <i class="ti ti-user text-gray-400 text-lg"></i>
                        My Profile
                    </a>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- Logout --}}
                <div class="py-1.5">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-3 
                                   px-4 py-2.5 text-sm text-red-600 
                                   hover:bg-red-50 transition-colors">
                            <i class="ti ti-logout text-lg"></i>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>