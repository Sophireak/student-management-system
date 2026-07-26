@extends('layouts.teacher', ['title' => 'Semester Report — ' . $class->name])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Semester Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        {{ $class->name }} · {{ $class->grade->name }}
        · {{ $semesterLabel }}
        · {{ $academicYear->name }}
    </p>
</div>

@php $activeYear = \App\Models\AcademicYear::where('is_active', true)->first(); @endphp

@include('partials.semester-report-filters', [
    'classes'          => $classes,
    'semesters'        => $semesters,
    'academicYears'    => collect(),
    'routePrefix'      => 'teacher',
    'selectedYearId'   => $activeYear?->id,
    'selectedClassId'  => $class->id,
    'selectedSemester' => $semester,
])

@if ($enrollments->isEmpty())
    <div class="bg-white rounded-lg border border-gray-200 px-5 py-8
                text-center text-gray-400 text-sm">
        No students enrolled.
    </div>
@elseif ($subjects->isEmpty())
    <div class="bg-white rounded-lg border border-gray-200 px-5 py-8
                text-center text-gray-400 text-sm">
        No subjects configured.
    </div>
@else
    {{-- Read-only view for teachers --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-gray-500 uppercase border-r border-gray-200
                               w-8 sticky left-0 bg-gray-50 z-10">No.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold
                               text-gray-500 uppercase border-r border-gray-200
                               w-48 sticky left-8 bg-gray-50 z-10">
                        Student Name
                    </th>
                    @foreach ($subjects as $subject)
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-500 uppercase border-r border-gray-100
                                   min-w-24">
                            {{ $subject->name }}
                        </th>
                    @endforeach
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-gray-500 uppercase bg-blue-50 min-w-20">
                        Total
                    </th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-gray-500 uppercase bg-blue-50 min-w-20">
                        Average
                    </th>
                    <th class="px-3 py-3 text-center text-xs font-semibold
                               text-gray-500 uppercase bg-yellow-50 min-w-16">
                        Rank
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($enrollments as $rowIndex => $enrollment)
                    @php $rowSummary = $summary[$enrollment->id] ?? []; @endphp
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
                        @foreach ($subjects as $subject)
                            @php $score = $matrix[$enrollment->id][$subject->id] ?? null; @endphp
                            <td class="px-3 py-2 text-center border-r border-gray-100
                                       text-gray-700">
                                @if ($subject->isNumeric())
                                    {{ $score?->score ?? '—' }}
                                @elseif ($subject->isGrade())
                                    {{ $score?->grade ?? '—' }}
                                @else
                                    {{ $score?->pass_fail ?? '—' }}
                                @endif
                            </td>
                        @endforeach
                        <td class="px-3 py-2 text-center bg-blue-50 font-semibold
                                   text-blue-800 text-sm">
                            {{ $rowSummary['total'] ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center bg-blue-50 font-semibold
                                   text-blue-800 text-sm">
                            {{ $rowSummary['average'] ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center bg-yellow-50 font-bold
                                   text-yellow-800 text-sm">
                            {{ $rowSummary['rank'] ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($isLocked)
        <div class="mt-3 px-4 py-2 bg-red-50 border border-red-200
                    rounded text-xs text-red-700">
            🔒 This semester report has been locked by the administrator.
        </div>
    @elseif (! $hasMonthlyData)
        <div class="mt-3 px-4 py-2 bg-yellow-50 border border-yellow-200
                    rounded text-xs text-yellow-700">
            Monthly scores for this semester have not been entered yet.
        </div>
    @endif
@endif

@endsection