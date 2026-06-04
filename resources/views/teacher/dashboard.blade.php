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
                    <a href="{{ route('teacher.student-attendance.index') }}"
                       class="text-xs px-2 py-1 bg-green-100 text-green-700
                              rounded hover:bg-green-200">
                        ✅ Attendance
                    </a>
                    <a href="{{ route('teacher.examination-scores.index') }}"
                       class="text-xs px-2 py-1 bg-blue-100 text-blue-700
                              rounded hover:bg-blue-200">
                        📝 Scores
                    </a>
                    <a href="{{ route('teacher.reports.ranking.index') }}"
                       class="text-xs px-2 py-1 bg-purple-100 text-purple-700
                              rounded hover:bg-purple-200">
                        📊 Reports
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200
                px-5 py-8 text-center text-gray-400 text-sm">
        No classes assigned to you for the active academic year.
        <p class="mt-1 text-xs">Contact your administrator.</p>
    </div>
@endif

@endsection