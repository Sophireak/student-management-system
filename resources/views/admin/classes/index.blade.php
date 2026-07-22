@extends('layouts.admin', ['title' => 'Classes'])

@section('content')

{{-- ========================================
     TOOLBAR
     ======================================== --}}
<x-admin.page-toolbar>
    <x-slot:left>
        <x-admin.toolbar-meta 
            icon="ti-building"
            label="Total Classes"
            value="{{ $classes->total() }} classes"
            color="purple" />
    </x-slot:left>

    <x-slot:right>
        <x-admin.toolbar-button 
            href="{{ route('admin.classes.create') }}"
            icon="ti-building-plus"
            label="Add Class"
            variant="primary" />
    </x-slot:right>
</x-admin.page-toolbar>

{{-- ========================================
     SEARCH + YEAR FILTER
     ======================================== --}}
<div class="bg-white p-2 rounded-2xl border border-gray-200 mb-5 shadow-sm">
    <form method="GET" action="{{ route('admin.classes.index') }}" class="flex flex-col sm:flex-row gap-2">

        {{-- Search --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="ti ti-search text-gray-400"></i>
            </div>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by class or grade name..."
                   class="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 
                          focus:ring-2 focus:ring-green-200 rounded-xl pl-10 pr-4 py-2.5 text-sm 
                          text-gray-700 transition-all placeholder-gray-400">
        </div>

        {{-- Year Filter --}}
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="ti ti-calendar text-gray-400"></i>
            </div>
            <select name="academic_year_id"
                    class="bg-gray-50 border-transparent focus:bg-white focus:border-green-500 
                           focus:ring-2 focus:ring-green-200 rounded-xl pl-10 pr-8 py-2.5 text-sm 
                           text-gray-700 transition-all appearance-none cursor-pointer min-w-[160px]">
                <option value="">All Years</option>
                @foreach ($academicYears as $year)
                    <option value="{{ $year->id }}" {{ ($yearId ?? '') == $year->id ? 'selected' : '' }}>
                        {{ $year->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="px-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-semibold 
                       rounded-xl border border-gray-200 transition-colors">
            Filter
        </button>

        @if (($search ?? false) || ($yearId ?? false))
            <a href="{{ route('admin.classes.index') }}" title="Clear filters"
               class="px-3 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold 
                      rounded-xl border border-red-100 transition-colors flex items-center">
                <i class="ti ti-x text-lg"></i>
            </a>
        @endif
    </form>
</div>

{{-- ========================================
     TABLE
     ======================================== --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Class
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Grade
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden md:table-cell">
                        Academic Year
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden sm:table-cell">
                        Students
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden lg:table-cell">
                        Capacity
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap text-right">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80">
                @forelse ($classes as $class)
                    @php
                        $enrolled    = $class->enrollments_count ?? 0;
                        $capacity    = $class->capacity ?? 0;
                        $fillPercent = $capacity > 0 ? round(($enrolled / $capacity) * 100) : 0;
                        $fillColor   = $fillPercent >= 90 ? 'red' : ($fillPercent >= 70 ? 'amber' : 'green');
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        
                        {{-- Class Name --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-100 to-indigo-100 
                                            text-purple-700 flex items-center justify-center font-bold shadow-inner flex-shrink-0">
                                    {{ strtoupper(substr($class->name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.classes.show', $class) }}" 
                                       class="text-sm font-bold text-gray-800 hover:text-green-600 transition-colors">
                                        {{ $class->name }}
                                    </a>
                                    <p class="text-[11px] text-gray-400 mt-0.5 md:hidden">
                                        {{ $class->academicYear->name }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Grade --}}
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                         font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                <i class="ti ti-award text-indigo-400"></i>
                                {{ $class->grade->name }}
                            </span>
                        </td>

                        {{-- Academic Year --}}
                        <td class="px-5 py-4 hidden md:table-cell">
                            <div class="flex items-center gap-1.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                                    <i class="ti ti-calendar text-gray-400"></i>
                                    {{ $class->academicYear->name }}
                                </span>
                                @if($class->academicYear->is_active)
                                    <span class="w-2 h-2 rounded-full bg-green-500" title="Active Year"></span>
                                @endif
                            </div>
                        </td>

                        {{-- Students Enrolled --}}
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <div class="flex flex-col gap-1.5 min-w-[100px]">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-semibold text-gray-700">
                                        {{ $enrolled }}
                                        @if($capacity > 0)
                                            <span class="text-xs font-normal text-gray-400">/ {{ $capacity }}</span>
                                        @endif
                                    </span>
                                    @if($capacity > 0)
                                        <span class="text-[10px] font-bold text-{{ $fillColor }}-600">
                                            {{ $fillPercent }}%
                                        </span>
                                    @endif
                                </div>
                                @if($capacity > 0)
                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                        <div class="h-1.5 rounded-full bg-{{ $fillColor }}-500 transition-all" 
                                             style="width: {{ min($fillPercent, 100) }}%">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Capacity --}}
                        <td class="px-5 py-4 hidden lg:table-cell">
                            @if($capacity > 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                                    <i class="ti ti-users text-gray-400"></i>
                                    {{ $capacity }} seats
                                </span>
                            @else
                                <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-lg border border-gray-100">
                                    No limit
                                </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.classes.show', $class) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-blue-50 hover:text-blue-600 transition-all border border-gray-100 hover:border-blue-100" 
                                   title="View Details">
                                    <i class="ti ti-eye text-lg"></i>
                                </a>

                                <a href="{{ route('admin.classes.edit', $class) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-amber-50 hover:text-amber-600 transition-all border border-gray-100 hover:border-amber-100" 
                                   title="Edit Class">
                                    <i class="ti ti-pencil text-lg"></i>
                                </a>

                                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                      onsubmit="return confirm('Are you sure you want to delete {{ $class->name }}? This action cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                                   hover:bg-red-50 hover:text-red-600 transition-all border border-gray-100 hover:border-red-100" 
                                            title="Delete Class">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <i class="ti ti-building-off text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800 mb-1">No classes found</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                @if($search || $yearId)
                                    No classes match your current filters.
                                @else
                                    Get started by creating your first class.
                                @endif
                            </p>
                            @if($search || $yearId)
                                <a href="{{ route('admin.classes.index') }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                                          rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    Clear Filters
                                </a>
                            @else
                                <a href="{{ route('admin.classes.create') }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 
                                          rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                                    <i class="ti ti-building-plus"></i> Create First Class
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
@if($classes->hasPages())
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
        {{ $classes->links() }}
    </div>
@endif

@endsection