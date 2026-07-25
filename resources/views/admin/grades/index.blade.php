@extends('layouts.admin', ['title' => 'Grades'])

@section('content')

{{-- ========================================
     TOOLBAR
     ======================================== --}}
<x-admin.page-toolbar>
    <x-slot:left>
        <x-admin.toolbar-meta 
            icon="ti-award"
            label="Total Grades"
            value="{{ $grades->total() }} grade levels"
            color="indigo" />
    </x-slot:left>

    <x-slot:right>
        <x-admin.toolbar-button 
            href="{{ route('admin.grades.create') }}"
            icon="ti-plus"
            label="Add Grade"
            variant="primary" />
    </x-slot:right>
</x-admin.page-toolbar>

{{-- ========================================
     SEARCH
     ======================================== --}}
<x-admin.toolbar-search 
    :action="route('admin.grades.index')"
    placeholder="Search by name or level..."
    :value="$search ?? ''" />

{{-- ========================================
     TABLE
     ======================================== --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Grade
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden sm:table-cell">
                        Level
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Classes
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden md:table-cell">
                        Subjects
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap text-right">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80">
                @forelse ($grades as $grade)
                    <tr class="hover:bg-gray-50/50 transition-colors group">

                        {{-- Grade Name --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 
                                            text-indigo-700 flex items-center justify-center font-extrabold 
                                            shadow-inner flex-shrink-0 text-sm">
                                    {{ $grade->level }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $grade->name }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-0.5 sm:hidden">
                                        Level {{ $grade->level }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Level Badge --}}
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                         font-mono font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                                <i class="ti ti-sort-ascending-numbers text-gray-400"></i>
                                Level {{ $grade->level }}
                            </span>
                        </td>

                        {{-- Classes Count --}}
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                         font-semibold bg-purple-50 text-purple-600 border border-purple-100">
                                <i class="ti ti-building text-purple-400"></i>
                                {{ $grade->classes_count }} {{ Str::plural('class', $grade->classes_count) }}
                            </span>
                        </td>

                        {{-- Subjects Count --}}
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                         font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                                <i class="ti ti-book text-blue-400"></i>
                                {{ $grade->subjects_count }} {{ Str::plural('subject', $grade->subjects_count) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.grades.edit', $grade) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-amber-50 hover:text-amber-600 transition-all border border-gray-100 hover:border-amber-100"
                                   title="Edit Grade">
                                    <i class="ti ti-pencil text-lg"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.grades.destroy', $grade) }}"
                                      onsubmit="return confirm('Are you sure you want to delete {{ $grade->name }}? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                                   hover:bg-red-50 hover:text-red-600 transition-all border border-gray-100 hover:border-red-100"
                                            title="Delete Grade">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <i class="ti ti-award-off text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800 mb-1">No grades found</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                @if($search)
                                    No grades match your search "{{ $search }}".
                                @else
                                    Get started by creating your first grade level.
                                @endif
                            </p>
                            @if($search)
                                <a href="{{ route('admin.grades.index') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300
                                          rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    Clear Search
                                </a>
                            @else
                                <a href="{{ route('admin.grades.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700
                                          rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                                    <i class="ti ti-plus"></i> Add First Grade
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
@if($grades->hasPages())
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
        {{ $grades->links() }}
    </div>
@endif

@endsection