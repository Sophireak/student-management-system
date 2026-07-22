@extends('layouts.teacher', ['title' => 'Annual Report'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Annual Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        View your class annual results.
    </p>
</div>

@php $activeYear = \App\Models\AcademicYear::where('is_active', true)->first(); @endphp

@include('partials.annual-report-filters', [
    'classes'         => $classes,
    'academicYears'   => collect(),
    'routePrefix'     => 'teacher',
    'selectedYearId'  => $activeYear?->id,
    'selectedClassId' => null,
])

@endsection