@php
    $colors = [
        'blue'   => 'bg-blue-50 text-blue-500',
        'green'  => 'bg-green-50 text-green-500',
        'purple' => 'bg-purple-50 text-purple-500',
        'yellow' => 'bg-yellow-50 text-yellow-500',
    ];
    $colorClass = $colors[$color] ?? $colors['blue'];
@endphp
<div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center {{ $colorClass }}">
        <i class="{{ $icon }} text-xl"></i>
    </div>
    <div>
        <p class="text-sm text-gray-500">{{ $label }}</p>
        <p class="text-2xl font-bold text-gray-800">{{ $value }}</p>
    </div>
</div>
