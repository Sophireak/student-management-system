@props(['icon', 'label', 'routes' => []])

@php
    $isActiveGroup = collect($routes)->some(fn($r) => request()->routeIs($r . '*'));
@endphp

<div x-data="{ open: {{ $isActiveGroup ? 'true' : 'false' }} }">
    <button
        @click="open = !open"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
               {{ $isActiveGroup ? 'bg-green-50 text-green-700' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}"
    >
        <i class="{{ $icon }} text-base {{ $isActiveGroup ? 'text-green-600' : 'text-gray-400' }}"></i>
        <span class="flex-1 text-left sidebar-label">{{ $label }}</span>
        <i class="ti ti-chevron-right text-sm transition-transform duration-200 sidebar-label"
           :class="open ? 'rotate-90' : ''"></i>
    </button>
    <div
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1"
        class="mt-0.5 ml-4 pl-3 border-l border-gray-100 space-y-0.5 sidebar-label"
    >
        {{ $slot }}
    </div>
</div>
