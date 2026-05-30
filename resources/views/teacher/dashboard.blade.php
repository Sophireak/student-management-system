@extends('layouts.admin', ['title' => 'Teacher Dashboard'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">
        Welcome, {{ auth()->user()->name }}
    </h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Here's an overview of your assigned classes.
    </p>
</div>

@if (isset($classes) && $classes->isNotEmpty())

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach ($classes as $class)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-semibold text-gray-800">
                            Class {{ $class->name }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $class->grade->name }}
                            · {{ $class->academicYear->name }}
                        </p>
                    </div>
                    <span class="text-2xl">🏛️</span>
                </div>
                <p class="text-sm text-gray-500">
                    <span class="font-semibold text-gray-700">
                        {{ $class->active_students }}
                    </span>
                    active students
                </p>
                <div class="flex items-center gap-2 mt-3">
    <a href="{{ route('teacher.attendance-sessions.create') }}"
       class="text-xs px-2 py-1 bg-green-100 text-green-700
              rounded hover:bg-green-200">
        Attendance
    </a>
    <a href="{{ route('teacher.examination-scores.index') }}"
       class="text-xs px-2 py-1 bg-blue-100 text-blue-700
              rounded hover:bg-blue-200">
        Scores
    </a>
</div>
            </div>
        @endforeach
    </div>

    @if (isset($recentSessions) && $recentSessions->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
                Recent Attendance Sessions
            </h3>
            @foreach ($recentSessions as $session)
                <div class="flex items-center justify-between py-2
                            border-b border-gray-100 last:border-0 text-sm">
                    <div>
                        <span class="font-medium text-gray-800">
                            {{ $session->schoolClass->name }}
                        </span>
                        <span class="text-gray-400 mx-1">·</span>
                        <span class="text-gray-500">
                            {{ $session->subject->name }}
                        </span>
                    </div>
                    <span class="text-xs text-gray-400">
                        {{ $session->session_date->format('M d, Y') }}
                    </span>
                </div>
            @endforeach
        </div>
    @endif

@else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200
                px-5 py-8 text-center text-gray-400 text-sm">
        No classes assigned to you for the active academic year.
        <p class="mt-1 text-xs">Contact your administrator.</p>
    </div>
@endif

@endsection