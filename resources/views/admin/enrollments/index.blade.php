@extends('layouts.admin', ['title' => 'Enrollments'])

@section('content')

@php
    $totalCount       = $totalCount ?? $enrollments->total();
    $activeCount      = $activeCount ?? 0;
    $transferredCount = $transferredCount ?? 0;
    $droppedCount     = $droppedCount ?? 0;
@endphp

{{-- ========================================
     TOOLBAR
     ======================================== --}}
<x-admin.page-toolbar>
    <x-slot:left>
        <x-admin.toolbar-tabs 
            filter-key="status"
            :tabs="[
                ['key' => 'all',         'label' => 'All',         'count' => $totalCount,       'icon' => 'ti-clipboard-list', 'color' => 'green'],
                ['key' => 'active',      'label' => 'Active',      'count' => $activeCount,      'icon' => 'ti-circle-check',   'color' => 'green'],
                ['key' => 'transferred', 'label' => 'Transferred', 'count' => $transferredCount, 'icon' => 'ti-transfer',       'color' => 'blue'],
                ['key' => 'dropped',     'label' => 'Dropped',     'count' => $droppedCount,     'icon' => 'ti-circle-x',       'color' => 'red'],
            ]" />
    </x-slot:left>

    <x-slot:right>
        <x-admin.toolbar-button 
            href="{{ route('admin.enrollments.create') }}"
            icon="ti-clipboard-plus"
            label="New Enrollment"
            variant="primary" />
    </x-slot:right>
</x-admin.page-toolbar>

{{-- ========================================
     SEARCH
     ======================================== --}}
<x-admin.toolbar-search 
    :action="route('admin.enrollments.index')"
    placeholder="Search by student name or ID..."
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
                        Student
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Class & Grade
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden md:table-cell">
                        Academic Year
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden lg:table-cell">
                        Enrolled On
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
                @forelse ($enrollments as $enrollment)
                    @php
                        $statusConfig = match($enrollment->status) {
                            'active'      => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-100', 'dot' => 'bg-green-500'],
                            'transferred' => ['bg' => 'bg-blue-50',  'text' => 'text-blue-700',  'border' => 'border-blue-100',  'dot' => 'bg-blue-500'],
                            'dropped'     => ['bg' => 'bg-red-50',   'text' => 'text-red-700',   'border' => 'border-red-100',   'dot' => 'bg-red-500'],
                            default       => ['bg' => 'bg-gray-50',  'text' => 'text-gray-600',  'border' => 'border-gray-200',  'dot' => 'bg-gray-400'],
                        };
                    @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors group">

                        {{-- Student --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold shadow-inner flex-shrink-0
                                            {{ $enrollment->student->gender === 'female'
                                                ? 'bg-gradient-to-br from-pink-100 to-rose-100 text-pink-700'
                                                : 'bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700' }}">
                                    {{ strtoupper(substr($enrollment->student->first_name, 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.students.show', $enrollment->student) }}"
                                       class="text-sm font-bold text-gray-800 hover:text-green-600 transition-colors">
                                        {{ $enrollment->student->full_name }}
                                    </a>
                                    <p class="text-[11px] font-mono text-gray-400 mt-0.5">
                                        {{ $enrollment->student->student_id }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Class & Grade --}}
                        <td class="px-5 py-4">
                            <div class="flex flex-col gap-1.5">
                                <span class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-700">
                                    <i class="ti ti-building text-gray-400 text-xs"></i>
                                    {{ $enrollment->schoolClass->name }}
                                </span>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[11px] 
                                             font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100 w-fit">
                                    <i class="ti ti-award text-indigo-400 text-[10px]"></i>
                                    {{ $enrollment->schoolClass->grade->name }}
                                </span>
                            </div>
                        </td>

                        {{-- Academic Year --}}
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                         font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                                <i class="ti ti-calendar text-gray-400"></i>
                                {{ $enrollment->schoolClass->academicYear->name }}
                            </span>
                        </td>

                        {{-- Enrolled On --}}
                        <td class="px-5 py-4 hidden lg:table-cell">
                            <div class="flex flex-col">
                                <span class="text-sm text-gray-600 font-medium">
                                    {{ $enrollment->enrolled_at->format('M d, Y') }}
                                </span>
                                <span class="text-[11px] text-gray-400">
                                    {{ $enrollment->enrolled_at->diffForHumans() }}
                                </span>
                            </div>
                        </td>

                        {{-- Status Badge --}}
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                         font-semibold border
                                         {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">

                                <a href="{{ route('admin.students.show', $enrollment->student) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-blue-50 hover:text-blue-600 transition-all border border-gray-100 hover:border-blue-100"
                                   title="View Student">
                                    <i class="ti ti-user text-lg"></i>
                                </a>

                                <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-amber-50 hover:text-amber-600 transition-all border border-gray-100 hover:border-amber-100"
                                   title="Edit Enrollment">
                                    <i class="ti ti-pencil text-lg"></i>
                                </a>

                                <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-purple-50 hover:text-purple-600 transition-all border border-gray-100 hover:border-purple-100"
                                   title="View Enrollment">
                                    <i class="ti ti-eye text-lg"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <i class="ti ti-clipboard-off text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800 mb-1">No enrollments found</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                @if($search || request('status'))
                                    No enrollments match your current filters.
                                @else
                                    Get started by enrolling your first student.
                                @endif
                            </p>
                            @if($search || request('status'))
                                <a href="{{ route('admin.enrollments.index') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300
                                          rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                    Clear Filters
                                </a>
                            @else
                                <a href="{{ route('admin.enrollments.create') }}"
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700
                                          rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                                    <i class="ti ti-clipboard-plus"></i> Enroll First Student
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
@if($enrollments->hasPages())
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
        {{ $enrollments->links() }}
    </div>
@endif

@endsection