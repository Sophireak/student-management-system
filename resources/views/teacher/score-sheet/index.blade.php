@extends('layouts.admin', ['title' => 'Score Sheet'])

@section('content')

<p class="text-sm text-gray-400 mb-4">Select a class and exam session to enter scores.</p>

@include('partials.score-sheet-filters', [
    'classes'         => $classes,
    'examSessions'    => $examSessions,
    'selectedClassId' => $selectedClassId,
    'routePrefix'     => 'teacher',
])

@endsection
