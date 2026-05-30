@extends('layouts.admin', ['title' => 'Score Sheet'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Score Sheet</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Select one of your classes and an exam session to enter scores.
    </p>
</div>

@include('partials.score-sheet-filters', [
    'classes'         => $classes,
    'examSessions'    => $examSessions,
    'selectedClassId' => $selectedClassId,
    'routePrefix'     => 'teacher',
])

@endsection