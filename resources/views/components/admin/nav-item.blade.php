@props(['route', 'icon', 'label'])

@php
    $isActive = request()->routeIs($route . '*');
@endphp

<a href="{{ route($route) }}"
   @class([
       'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium',
       'bg-white/20 text-white shadow-sm' => $isActive,
       'text-green-100 hover:bg-white/10 hover:text-white transition-colors duration-150' => !$isActive,
   ])
>
    <i @class([
        $icon, 'text-lg',
        'text-white' => $isActive,
        'text-green-200' => !$isActive,
    ])></i>
    <span class="sidebar-label">{{ $label }}</span>
</a>