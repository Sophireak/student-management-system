<a href="{{ route($route) }}"
   class="flex items-center gap-3 px-3 py-2 rounded-md text-sm font-medium transition-colors
          {{ $isActive()
              ? 'bg-gray-700 text-white'
              : 'text-gray-300 hover:bg-gray-700 hover:text-white' }}"
>
    <span class="text-base leading-none">{{ $icon }}</span>
    <span>{{ $label }}</span>
</a>