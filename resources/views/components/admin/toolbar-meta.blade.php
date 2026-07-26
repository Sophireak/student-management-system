@props([
    'icon'   => 'ti-info-circle',
    'label'  => 'Total',
    'value'  => '0',
    'color'  => 'green',
])

<div class="inline-flex items-center gap-2 bg-white border border-gray-200 
            rounded-xl px-4 py-2 shadow-sm">
    <div class="w-8 h-8 rounded-lg bg-{{ $color }}-50 
                flex items-center justify-center">
        <i class="ti {{ $icon }} text-{{ $color }}-600 text-base"></i>
    </div>
    <div>
        <p class="text-xs text-gray-400 leading-tight">{{ $label }}</p>
        <p class="text-sm font-bold text-gray-800 leading-tight">
            {{ $value }}
        </p>
    </div>
</div>