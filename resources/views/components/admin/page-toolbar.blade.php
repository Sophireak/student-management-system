{{-- 
    Usage:
    <x-admin.page-toolbar>
        <x-slot:left>...</x-slot:left>
        <x-slot:right>...</x-slot:right>
    </x-admin.page-toolbar>
--}}

<div class="flex flex-col sm:flex-row sm:items-center 
            justify-between gap-3 mb-4">
    <div class="flex-1 min-w-0">
        {{ $left ?? '' }}
    </div>
    <div class="flex items-center gap-2 flex-shrink-0">
        {{ $right ?? '' }}
    </div>
</div>