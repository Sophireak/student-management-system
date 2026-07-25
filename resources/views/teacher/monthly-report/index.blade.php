@extends('layouts.teacher', ['title' => 'Monthly Reports'])

@section('content')

<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
            <i class="ti ti-calendar-stats text-green-600 text-xl"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-gray-800 leading-tight">Monthly Score Report</h1>
            <span class="inline-flex items-center gap-1 mt-0.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
                <i class="ti ti-filter text-sm"></i> Select your class and month
            </span>
        </div>
    </div>
</div>

@php
    $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
@endphp

@include('partials.monthly-report-filters-teacher', [
    'classes'         => $classes,
    'months'          => $months,
    'academicYears'   => collect(),
    'routePrefix'     => 'teacher',
    'selectedYearId'  => $activeYear?->id,
    'selectedClassId' => null,
    'selectedMonth'   => null,
])

@endsection