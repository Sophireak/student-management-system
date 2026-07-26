@php
    $colors = [
        'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'text-blue-500',   'ring' => 'ring-blue-100'],
        'green'  => ['bg' => 'bg-green-50',  'icon' => 'text-green-500',  'ring' => 'ring-green-100'],
        'purple' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-500', 'ring' => 'ring-purple-100'],
        'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'text-yellow-500', 'ring' => 'ring-yellow-100'],
        'red'    => ['bg' => 'bg-red-50',    'icon' => 'text-red-500',    'ring' => 'ring-red-100'],
    ];
    $c = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white rounded-xl border border-gray-200 p-5 
            hover:shadow-md hover:border-gray-300 
            transition-all duration-200">
    <div class="flex items-center justify-between mb-3">
        <div class="w-11 h-11 rounded-xl flex items-center justify-center 
                    {{ $c['bg'] }} ring-4 {{ $c['ring'] }}">
            <i class="{{ $icon }} text-xl {{ $c['icon'] }}"></i>
        </div>
        @if(isset($trend))
            <span class="text-xs font-medium px-2 py-0.5 rounded-full
                         {{ $trend >= 0 ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }}">
                {{ $trend >= 0 ? '+' : '' }}{{ $trend }}%
            </span>
        @endif
    </div>
    <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
    <p class="text-sm text-gray-400 mt-0.5">{{ $label }}</p>
</div>