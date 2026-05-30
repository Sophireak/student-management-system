@extends('layouts.admin', ['title' => 'Semester Reports'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Semester Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Select a class and semester. Calculate scores from monthly data,
        then review and adjust before locking.
    </p>
</div>

@include('partials.semester-report-filters', [
    'classes'           => $classes,
    'semesters'         => $semesters,
    'academicYears'     => $academicYears,
    'routePrefix'       => 'admin',
    'selectedYearId'    => null,
    'selectedClassId'   => null,
    'selectedSemester'  => null,
])

@endsection