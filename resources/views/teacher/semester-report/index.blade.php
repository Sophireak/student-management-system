@extends('layouts.teacher', ['title' => 'Semester Reports'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Semester Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        View your class semester scores. Contact admin to make corrections.
    </p>
</div>

@php $activeYear = \App\Models\AcademicYear::where('is_active', true)->first(); @endphp

@include('partials.semester-report-filters', [
    'classes'          => $classes,
    'semesters'        => $semesters,
    'academicYears'    => collect(),
    'routePrefix'      => 'teacher',
    'selectedYearId'   => $activeYear?->id,
    'selectedClassId'  => null,
    'selectedSemester' => null,
])

@endsection