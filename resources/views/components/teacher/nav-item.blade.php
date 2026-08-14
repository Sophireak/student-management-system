@props(['route', 'icon', 'label', 'matches' => null])

@php
    if ($matches) {
        $isActive = collect($matches)->contains(fn($r) => request()->routeIs($r . '*'));
    } else {
        $isActive = request()->routeIs($route . '*');
    }
@endphp

<a href="{{ route($route) }}"
   @class([
       'flex flex-col items-center justify-center gap-0.5 
        py-1.5 px-3 rounded-2xl transition-all min-w-[3.5rem]',
       'bg-gradient-to-br from-green-500 to-amber-400 shadow-sm shadow-green-900/20' => $isActive,
       'text-gray-500 hover:text-gray-700' => !$isActive,
   ])
>
    <i @class([
        $icon, 'text-xl',
        'text-white' => $isActive,
        'text-gray-400' => !$isActive,
    ])></i>
    <span @class([
        'text-[10px] font-medium leading-tight',
        'text-white' => $isActive,
        'text-gray-400' => !$isActive,
    ])>
        {{ $label }}
    </span>
</a>
