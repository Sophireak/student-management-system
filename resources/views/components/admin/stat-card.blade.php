@php
    $colors = [
        'blue'   => 'bg-blue-50 text-blue-600',
        'green'  => 'bg-green-50 text-green-600',
        'purple' => 'bg-purple-50 text-purple-600',
        'yellow' => 'bg-yellow-50 text-yellow-600',
    ];
    $colorClass = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4">
    <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-2xl {{ $colorClass }}">
        {{ $icon }}
    </div>
    <div>
        <p class="text-sm text-gray-500">{{ $label }}</p>
        <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
    </div>
</div>