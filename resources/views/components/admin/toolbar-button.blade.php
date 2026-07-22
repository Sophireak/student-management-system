@props([
    'href'    => '#',
    'icon'    => null,
    'label'   => 'Button',
    'variant' => 'primary', // primary, secondary, ghost
    'badge'   => null,
])

@php
    $classes = match($variant) {
        'primary'   => 'bg-green-600 hover:bg-green-700 text-white shadow-sm hover:shadow-green-500/20',
        'secondary' => 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300',
        'ghost'     => 'text-gray-600 hover:bg-gray-100',
        default     => 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50',
    };
@endphp

<a href="{{ $href }}"
   class="inline-flex items-center gap-2 px-4 py-2.5 
          text-sm font-semibold rounded-xl transition-all 
          active:scale-[0.98] {{ $classes }}">
    @if ($icon)
        <i class="ti {{ $icon }} text-base"></i>
    @endif
    <span class="hidden sm:inline">{{ $label }}</span>
    @if ($badge)
        <span class="px-1.5 py-0.5 text-[10px] font-bold 
                     rounded-full bg-red-50 text-red-600 
                     border border-red-100">
            {{ $badge }}
        </span>
    @endif
</a>