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
        py-1 px-2 rounded-xl transition-colors min-w-[3.5rem]',
       'text-green-600' => $isActive,
       'text-gray-400 hover:text-gray-600' => !$isActive,
   ])
>
    <i @class([
        $icon, 'text-xl',
        'text-green-600' => $isActive,
        'text-gray-400' => !$isActive,
    ])></i>
    <span @class([
        'text-[10px] font-medium leading-tight',
        'text-green-600' => $isActive,
        'text-gray-400' => !$isActive,
    ])>
        {{ $label }}
    </span>
</a>