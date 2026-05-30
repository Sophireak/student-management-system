@extends('layouts.admin', ['title' => 'Monthly Reports'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Monthly Score Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Select academic year, class and month to view or enter scores.
    </p>
</div>

@include('partials.monthly-report-filters', [
    'classes'         => $classes,
    'months'          => $months,
    'academicYears'   => $academicYears,
    'routePrefix'     => 'admin',
    'selectedYearId'  => null,
    'selectedClassId' => null,
    'selectedMonth'   => null,
])

@endsection