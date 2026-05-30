@extends('layouts.admin', ['title' => 'Score Sheet'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Score Sheet</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Select a class and exam session to enter or edit scores.
    </p>
</div>

@include('partials.score-sheet-filters', [
    'classes'         => $classes,
    'examSessions'    => $examSessions,
    'selectedClassId' => $selectedClassId,
    'routePrefix'     => 'admin',
])

@endsection