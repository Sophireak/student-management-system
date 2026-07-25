@if ($paginator->hasPages())
    <nav class="flex items-center justify-between gap-4">

        {{-- Left: Showing results info --}}
        <p class="text-xs text-gray-400 hidden sm:block">
            Showing 
            <span class="font-semibold text-gray-600">
                {{ $paginator->firstItem() }}
            </span>
            –
            <span class="font-semibold text-gray-600">
                {{ $paginator->lastItem() }}
            </span>
            of
            <span class="font-semibold text-gray-600">
                {{ $paginator->total() }}
            </span>
            results
        </p>

        {{-- Right: Page buttons --}}
        <div class="flex items-center gap-1 ml-auto">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center 
                             w-9 h-9 rounded-xl text-sm
                             text-gray-300 bg-gray-50 
                             border border-gray-100 
                             cursor-not-allowed">
                    <i class="ti ti-chevron-left text-sm"></i>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="inline-flex items-center justify-center 
                          w-9 h-9 rounded-xl text-sm
                          text-gray-500 bg-white 
                          border border-gray-200
                          hover:bg-green-50 hover:text-green-600 
                          hover:border-green-200 transition-all">
                    <i class="ti ti-chevron-left text-sm"></i>
                </a>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center 
                                 w-9 h-9 rounded-xl text-sm 
                                 text-gray-400">
                        ...
                    </span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center 
                                         w-9 h-9 rounded-xl text-sm font-bold
                                         bg-green-600 text-white 
                                         border border-green-600">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center 
                                      w-9 h-9 rounded-xl text-sm font-medium
                                      text-gray-600 bg-white 
                                      border border-gray-200
                                      hover:bg-green-50 hover:text-green-600 
                                      hover:border-green-200 transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="inline-flex items-center justify-center 
                          w-9 h-9 rounded-xl text-sm
                          text-gray-500 bg-white 
                          border border-gray-200
                          hover:bg-green-50 hover:text-green-600 
                          hover:border-green-200 transition-all">
                    <i class="ti ti-chevron-right text-sm"></i>
                </a>
            @else
                <span class="inline-flex items-center justify-center 
                             w-9 h-9 rounded-xl text-sm
                             text-gray-300 bg-gray-50 
                             border border-gray-100 
                             cursor-not-allowed">
                    <i class="ti ti-chevron-right text-sm"></i>
                </span>
            @endif

        </div>
    </nav>
@endif