@props(['tabs' => []])

{{-- 
    Usage:
    <x-admin.toolbar-tabs :tabs="[
        ['key' => 'all',    'label' => 'All',    'count' => 120, 'icon' => 'ti-users',         'color' => 'green'],
        ['key' => 'male',   'label' => 'Male',   'count' => 60,  'icon' => 'ti-gender-male',   'color' => 'blue'],
        ['key' => 'female', 'label' => 'Female', 'count' => 60,  'icon' => 'ti-gender-female', 'color' => 'pink'],
    ]" />
--}}

@php
    $currentFilter = request($attributes->get('filter-key', 'filter'), 'all');
    $filterKey     = $attributes->get('filter-key', 'filter');
@endphp

<div class="inline-flex items-center bg-white border border-gray-200 
            rounded-xl p-1 shadow-sm overflow-x-auto">

    @foreach ($tabs as $tab)
        @php
            $isActive = $currentFilter === $tab['key'];
            $color    = $tab['color'] ?? 'green';
            $params   = array_merge(
                request()->query(), 
                [$filterKey => $tab['key'] === 'all' ? null : $tab['key']]
            );
        @endphp

        <a href="{{ url()->current() }}?{{ http_build_query($params) }}"
           class="flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap
                  {{ $isActive 
                     ? "bg-{$color}-50 text-{$color}-700" 
                     : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
            @isset($tab['icon'])
                <i class="ti {{ $tab['icon'] }} text-base"></i>
            @endisset
            {{ $tab['label'] }}
            <span class="text-xs font-bold px-1.5 py-0.5 rounded-md
                         {{ $isActive 
                            ? "bg-{$color}-100 text-{$color}-700" 
                            : 'bg-gray-100 text-gray-500' }}">
                {{ $tab['count'] }}
            </span>
        </a>
    @endforeach
</div>