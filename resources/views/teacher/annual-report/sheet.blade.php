@extends('layouts.teacher', ['title' => 'Annual Report — ' . $class->name])

@section('content')

<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 shadow-sm flex items-center justify-between">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
            <i class="ti ti-calendar-event text-green-600 text-xl"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-gray-800 leading-tight">Annual Report</h1>
            <p class="text-xs text-gray-500 mt-0.5">
                {{ $class->name }} · {{ $class->grade->name }} · {{ $academicYear->name }}
            </p>
        </div>
    </div>
    @if ($isLocked)
        <span class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold bg-red-50 border border-red-200
                     text-red-700 rounded-full">
            <i class="ti ti-lock text-sm"></i> Final — Locked
        </span>
    @endif
</div>

@php $activeYear = \App\Models\AcademicYear::where('is_active', true)->first(); @endphp

@include('partials.annual-report-filters-teacher', [
    'classes'         => $classes,
    'academicYears'   => collect(),
    'routePrefix'     => 'teacher',
    'selectedYearId'  => $activeYear?->id,
    'selectedClassId' => $class->id,
])

@if ($enrollments->isEmpty())
    <div class="bg-white rounded-lg border border-gray-200 px-5 py-8
                text-center text-gray-400 text-sm">
        No students enrolled.
    </div>
@else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200
                overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-gray-500 uppercase border-r border-gray-200
                               w-8 sticky left-0 bg-gray-50 z-10">No.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold
                               text-gray-500 uppercase border-r border-gray-200
                               w-44 sticky left-8 bg-gray-50 z-10">
                        Student Name
                    </th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-blue-600 uppercase bg-blue-50 border-r
                               border-gray-200 min-w-20">S1 Avg</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-blue-600 uppercase bg-blue-50 border-r
                               border-gray-200 min-w-32">S1 Conduct</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-purple-600 uppercase bg-purple-50 border-r
                               border-gray-200 min-w-20">S2 Avg</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-purple-600 uppercase bg-purple-50 border-r
                               border-gray-200 min-w-32">S2 Conduct</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-gray-700 uppercase bg-gray-100 border-r
                               border-gray-200 min-w-24">Final</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-gray-700 uppercase bg-gray-100 border-r
                               border-gray-200 min-w-16">Rank</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-gray-700 uppercase bg-gray-100 border-r
                               border-gray-200 min-w-16">Pass</th>
                    <th class="px-3 py-3 text-left text-xs font-semibold
                               text-gray-500 uppercase min-w-36">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($enrollments as $rowIndex => $enrollment)
                    @php $row = $existing->get($enrollment->id); @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-center text-xs text-gray-400
                                   border-r border-gray-200
                                   sticky left-0 bg-white z-10">
                            {{ $rowIndex + 1 }}
                        </td>
                        <td class="px-4 py-2 font-medium text-gray-800
                                   border-r border-gray-200 whitespace-nowrap
                                   sticky left-8 bg-white z-10">
                            {{ $enrollment->student->full_name }}
                        </td>
                        <td class="px-3 py-2 text-center bg-blue-50 text-sm
                                   border-r border-gray-200 font-medium">
                            {{ $row?->semester1_avg ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center bg-blue-50 text-xs
                                   border-r border-gray-200">
                            {{ $row?->semester1_conduct ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center bg-purple-50 text-sm
                                   border-r border-gray-200 font-medium">
                            {{ $row?->semester2_avg ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center bg-purple-50 text-xs
                                   border-r border-gray-200">
                            {{ $row?->semester2_conduct ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center bg-gray-100 font-bold
                                   text-gray-800 text-sm border-r border-gray-200">
                            {{ $row?->final_score ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center bg-gray-100 font-bold
                                   text-blue-700 text-sm border-r border-gray-200">
                            {{ $row?->rank ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center bg-gray-100 text-sm
                                   border-r border-gray-200">
                            @if ($row?->is_passing === true)
                                <span class="text-green-600 font-bold">✓</span>
                            @elseif ($row?->is_passing === false)
                                <span class="text-red-600 font-bold">✗</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-xs text-gray-500">
                            {{ $row?->notes ?? '' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection