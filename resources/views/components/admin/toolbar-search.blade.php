@props([
    'action'      => '',
    'placeholder' => 'Search...',
    'value'       => '',
    'preserve'    => [],  // extra params to preserve on clear
])

<div class="bg-white p-2 rounded-2xl border border-gray-200 
            mb-5 shadow-sm">
    <form method="GET" action="{{ $action }}" class="flex gap-2">

        {{-- Preserve extra filters --}}
        @foreach ($preserve as $key => $val)
            @if ($val)
                <input type="hidden" name="{{ $key }}" value="{{ $val }}">
            @endif
        @endforeach

        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 
                        flex items-center pointer-events-none">
                <i class="ti ti-search text-gray-400"></i>
            </div>
            <input type="text" 
                   name="search" 
                   value="{{ $value }}"
                   placeholder="{{ $placeholder }}"
                   class="w-full bg-gray-50 border border-transparent 
                          focus:bg-white focus:border-green-500 
                          focus:ring-2 focus:ring-green-200 
                          rounded-xl pl-10 pr-4 py-2.5 text-sm 
                          text-gray-700 transition-all 
                          placeholder-gray-400">
        </div>

        <button type="submit"
                class="px-4 py-2.5 bg-gray-50 hover:bg-gray-100 
                       text-gray-700 text-sm font-semibold 
                       rounded-xl border border-gray-200 
                       transition-colors whitespace-nowrap">
            Search
        </button>

        @if ($value)
            <a href="{{ $action }}?{{ http_build_query(array_filter($preserve)) }}"
               title="Clear search"
               class="px-3 py-2.5 bg-red-50 hover:bg-red-100 
                      text-red-600 text-sm font-semibold 
                      rounded-xl border border-red-100 
                      transition-colors flex items-center 
                      flex-shrink-0">
                <i class="ti ti-x text-lg"></i>
            </a>
        @endif
    </form>
</div>