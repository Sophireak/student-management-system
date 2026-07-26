@props(['icon', 'label'])

@php
    $isActive = $isActiveGroup();
@endphp

<div x-data="{ open: {{ $isActive ? 'true' : 'false' }} }">
    <button
        @click="expandIfCollapsed(); open = !open"
        @class([
            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium',
            'bg-white/20 text-white' => $isActive,
            'text-green-100 hover:bg-white/10 hover:text-white transition-colors duration-150' => !$isActive,
        ])
    >
        <i @class([
            $icon, 'text-lg',
            'text-white' => $isActive,
            'text-green-200' => !$isActive,
        ])></i>
        <span class="flex-1 text-left sidebar-label">
            {{ $label }}
        </span>
        <i class="ti ti-chevron-right text-sm text-green-200 
                  transition-transform duration-200 sidebar-label"
           :class="open ? 'rotate-90' : ''"
        ></i>
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
        class="mt-1 ml-4 pl-3 border-l border-green-400/30 
               space-y-0.5 sidebar-group-children"
    >
        {{ $slot }}
    </div>
</div>