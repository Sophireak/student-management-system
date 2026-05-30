@extends('layouts.admin', ['title' => 'Annual Report'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Annual Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Calculate final scores from semester data, review, adjust, then lock.
    </p>
</div>

@include('partials.annual-report-filters', [
    'classes'         => $classes,
    'academicYears'   => $academicYears,
    'routePrefix'     => 'admin',
    'selectedYearId'  => null,
    'selectedClassId' => null,
])

@endsection