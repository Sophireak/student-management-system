<a href="{{ route($route) }}"
   class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
          {{ $isActive()
              ? 'bg-green-50 text-green-700'
              : 'text-gray-500 hover:bg-gray-50 hover:text-gray-800' }}"
>
    <i class="{{ $icon }} text-base {{ $isActive() ? 'text-green-600' : 'text-gray-400' }}"></i>
    <span>{{ $label }}</span>
</a>
