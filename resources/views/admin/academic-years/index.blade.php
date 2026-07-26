@extends('layouts.admin', ['title' => 'Academic Years'])

@section('content')

@php
    $currentFilter = request('status', 'all');
    $totalCount    = $totalCount ?? $academicYears->total();
    $activeCount   = $activeCount ?? 0;
    $inactiveCount = $inactiveCount ?? 0;
@endphp

{{-- ========================================
     TOOLBAR
     ======================================== --}}
<x-admin.page-toolbar>
    <x-slot:left>
        <x-admin.toolbar-tabs 
            filter-key="status"
            :tabs="[
                ['key' => 'all',      'label' => 'All',      'count' => $totalCount,    'icon' => 'ti-calendar',       'color' => 'green'],
                ['key' => 'active',   'label' => 'Active',   'count' => $activeCount,   'icon' => 'ti-circle-check',   'color' => 'green'],
                ['key' => 'inactive', 'label' => 'Inactive', 'count' => $inactiveCount, 'icon' => 'ti-circle-dashed',  'color' => 'gray'],
            ]" />
    </x-slot:left>

    <x-slot:right>
        <x-admin.toolbar-button 
            href="{{ route('admin.academic-years.create') }}"
            icon="ti-calendar-plus"
            label="Add Year"
            variant="primary" />
    </x-slot:right>
</x-admin.page-toolbar>

{{-- ========================================
     SEARCH
     ======================================== --}}
<x-admin.toolbar-search 
    :action="route('admin.academic-years.index')"
    placeholder="Search academic years..."
    :value="$search ?? ''"
    :preserve="['status' => request('status')]" />

{{-- ========================================
     TABLE
     ======================================== --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Academic Year
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden sm:table-cell">
                        Period
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Status
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap text-right">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80">
                @forelse ($academicYears as $year)
                    <tr class="hover:bg-gray-50/50 transition-colors group">

                        {{-- Year Name --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center 
                                            font-bold flex-shrink-0
                                            {{ $year->is_active 
                                                ? 'bg-gradient-to-br from-green-100 to-emerald-100 text-green-700' 
                                                : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-500' }}">
                                    <i class="ti ti-calendar text-lg"></i>
                                </div>
                                <div>
                                    <a href="{{ route('admin.academic-years.show', $year) }}"
                                       class="text-sm font-bold text-gray-800 hover:text-green-600 transition-colors">
                                        {{ $year->name }}
                                    </a>
                                    <p class="text-[11px] text-gray-400 mt-0.5 sm:hidden">
                                        {{ $year->start_date?->format('M Y') }} — {{ $year->end_date?->format('M Y') }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Period --}}
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                    <i class="ti ti-calendar-event text-gray-400 text-xs"></i>
                                    {{ $year->start_date?->format('M d, Y') ?? '—' }}
                                </div>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <i class="ti ti-calendar-due text-gray-400 text-xs"></i>
                                    {{ $year->end_date?->format('M d, Y') ?? '—' }}
                                </div>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-5 py-4">
                            @if($year->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-bold bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-bold bg-gray-50 text-gray-500 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.academic-years.show', $year) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-blue-50 hover:text-blue-600 transition-all border border-gray-100 hover:border-blue-100"
                                   title="View Details">
                                    <i class="ti ti-eye text-lg"></i>
                                </a>
                                <a href="{{ route('admin.academic-years.edit', $year) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-amber-50 hover:text-amber-600 transition-all border border-gray-100 hover:border-amber-100"
                                   title="Edit Year">
                                    <i class="ti ti-pencil text-lg"></i>
                                </a>
                                @if(!$year->is_active)
                                    <form method="POST" action="{{ route('admin.academic-years.activate', $year) }}"
                                          onsubmit="return confirm('Set {{ $year->name }} as the active year? This will deactivate the current active year.')">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                                       hover:bg-green-50 hover:text-green-600 transition-all border border-gray-100 hover:border-green-100"
                                                title="Activate Year">
                                            <i class="ti ti-check text-lg"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <i class="ti ti-calendar-off text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800 mb-1">No academic years found</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                @if($search)
                                    No years match your search "{{ $search }}".
                                @else
                                    Get started by creating your first academic year.
                                @endif
                            </p>
                            @if($search)
                                <a href="{{ route('admin.academic-years.index') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300
                                          rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    Clear Search
                                </a>
                            @else
                                <a href="{{ route('admin.academic-years.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700
                                          rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                                    <i class="ti ti-calendar-plus"></i> Add First Year
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($academicYears->hasPages())
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
        {{ $academicYears->links() }}
    </div>
@endif

@endsection