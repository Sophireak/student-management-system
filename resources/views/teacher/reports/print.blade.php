@extends('layouts.print', ['title' => 'Report — ' . $class->name])

@section('content')

{{-- Route to admin partials (reuse) --}}
@include('admin.reports.partials.' . $reportType, [
    'class'       => $class,
    'subjects'    => $subjects,
    'enrollments' => $enrollments,
    'matrix'      => $matrix,
    'summary'     => $summary,
    'statistics'  => $statistics,
    'period'      => $period,
    'periodLabel' => $periodLabel,
    'academicYear' => $academicYear,
])

@endsection