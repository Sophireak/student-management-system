@extends('layouts.admin', ['title' => 'Monthly Reports'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Monthly Score Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Select your class and month to enter scores.
    </p>
</div>

@php
    $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
@endphp

@include('partials.monthly-report-filters', [
    'classes'         => $classes,
    'months'          => $months,
    'academicYears'   => collect(),
    'routePrefix'     => 'teacher',
    'selectedYearId'  => $activeYear?->id,
    'selectedClassId' => null,
    'selectedMonth'   => null,
])

@endsection