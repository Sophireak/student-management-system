@extends('layouts.teacher', ['title' => 'Dashboard'])

@section('content')

@php
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 18 ? 'Good Afternoon' : 'Good Evening');
    $emoji    = $hour < 12 ? '☀️' : ($hour < 18 ? '👋' : '🌙');

    // Smart action states
    $hasClasses     = $classes->isNotEmpty();
    $needAttendance = $todayAttendance && !$todayAttendance['taken'] && $hasClasses;
    $needScores     = $scoreProgress && $scoreProgress['percent'] < 100;
    $canViewReport  = $scoreProgress && $scoreProgress['percent'] >= 50;
@endphp

{{-- Greeting Header --}}
<div class="mb-5">
    <h1 class="text-xl font-bold text-gray-800">
        {{ $greeting }}, {{ auth()->user()->name }} {{ $emoji }}
    </h1>
    <p class="text-xs text-gray-400 mt-0.5">
        {{ now()->format('l, F d, Y') }}
    </p>
</div>

{{-- Stat Cards (Clickable) --}}
<div class="grid grid-cols-3 gap-3 mb-6">

    {{-- Classes --}}
    <a href="#classes-list"
       class="bg-white rounded-xl border border-gray-200 p-4 text-center 
              hover:border-green-200 hover:shadow-sm transition-all">
        <div class="w-10 h-10 rounded-xl bg-green-50 ring-4 ring-green-100 
                    flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-building text-green-500 text-lg"></i>
        </div>
        <p class="text-xl font-bold text-gray-800">
            {{ $classes->count() }}
        </p>
        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">
            Classes
        </p>
    </a>

    {{-- Students --}}
    <a href="{{ route('teacher.students.index') }}"
       class="bg-white rounded-xl border border-gray-200 p-4 text-center 
              hover:border-blue-200 hover:shadow-sm transition-all">
        <div class="w-10 h-10 rounded-xl bg-blue-50 ring-4 ring-blue-100 
                    flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-users text-blue-500 text-lg"></i>
        </div>
        <p class="text-xl font-bold text-gray-800">
            {{ $totalStudents }}
        </p>
        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">
            Students
        </p>
    </a>

    {{-- Sessions --}}
    <a href="{{ route('teacher.student-attendance.index') }}"
       class="bg-white rounded-xl border border-gray-200 p-4 text-center 
              hover:border-purple-200 hover:shadow-sm transition-all">
        <div class="w-10 h-10 rounded-xl bg-purple-50 ring-4 ring-purple-100 
                    flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-calendar-check text-purple-500 text-lg"></i>
        </div>
        <p class="text-xl font-bold text-gray-800">
            {{ $totalSessions }}
        </p>
        <p class="text-[10px] text-gray-400 font-medium uppercase tracking-wide">
            Sessions
        </p>
    </a>

</div>

{{-- Smart Contextual Actions --}}
@if ($hasClasses)
<div class="mb-6">
    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-1">
        What to do next
    </h2>

    <div class="space-y-2">

        {{-- Attendance Task --}}
        @if ($needAttendance)
            <a href="{{ route('teacher.student-attendance.index') }}"
               class="flex items-center gap-3 bg-white border border-amber-200 rounded-xl p-4 
                      hover:border-amber-300 hover:shadow-sm transition-all">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-calendar-check text-amber-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">Take Today's Attendance</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $classes->first()->name }} · {{ $totalStudents }} students waiting
                    </p>
                </div>
                <i class="ti ti-chevron-right text-amber-500 text-lg flex-shrink-0"></i>
            </a>
        @endif

        {{-- Score Entry Task --}}
        @if ($needScores)
            <a href="{{ route('teacher.scores.index', [
                    'class_id' => $scoreProgress['class']->id,
                    'period'   => 'month_' . $scoreProgress['month'],
                ]) }}"
               class="flex items-center gap-3 bg-white border border-blue-200 rounded-xl p-4 
                      hover:border-blue-300 hover:shadow-sm transition-all">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-pencil text-blue-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">Continue Score Entry</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $scoreProgress['month_name'] }} · 
                        {{ $scoreProgress['filled'] }}/{{ $scoreProgress['total'] }} done ({{ $scoreProgress['percent'] }}%)
                    </p>
                </div>
                <i class="ti ti-chevron-right text-blue-500 text-lg flex-shrink-0"></i>
            </a>
        @endif

        {{-- View Report --}}
        @if ($canViewReport)
            <a href="{{ route('teacher.reports.index') }}"
               class="flex items-center gap-3 bg-white border border-purple-200 rounded-xl p-4 
                      hover:border-purple-300 hover:shadow-sm transition-all">
                <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-chart-bar text-purple-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800">View Class Report</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ $scoreProgress['month_name'] }} results ready
                    </p>
                </div>
                <i class="ti ti-chevron-right text-purple-500 text-lg flex-shrink-0"></i>
            </a>
        @endif

        {{-- All done --}}
        @if (!$needAttendance && !$needScores && !$canViewReport)
            <div class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4">
                <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-check text-green-600 text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-green-800">All caught up! 🎉</p>
                    <p class="text-xs text-green-600 mt-0.5">
                        Nothing urgent to do right now.
                    </p>
                </div>
            </div>
        @endif

    </div>
</div>
@endif

{{-- My Classes --}}
<div class="mb-6" id="classes-list">
    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-1">
        My Classes
    </h2>

    @if ($classes->isNotEmpty())
        <div class="space-y-2">
            @foreach ($classes as $class)
                <div class="bg-white rounded-xl border border-gray-200 
                            p-4 flex items-center justify-between
                            hover:border-green-200 hover:shadow-sm 
                            transition-all">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-green-50 
                                    flex items-center justify-center 
                                    flex-shrink-0">
                            <i class="ti ti-building text-green-600 text-lg"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-800 truncate">
                                {{ $class->name }}
                            </p>
                            <p class="text-xs text-gray-400 truncate">
                                {{ $class->grade->name }} · 
                                {{ $class->academicYear->name }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 
                                rounded-lg bg-gray-50 flex-shrink-0 ml-2">
                        <i class="ti ti-users text-gray-400 text-sm"></i>
                        <span class="text-sm font-semibold text-gray-600">
                            {{ $class->active_students }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 
                    px-5 py-10 text-center">
            <div class="w-14 h-14 rounded-full bg-gray-50 
                        flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-building-off text-2xl text-gray-300"></i>
            </div>
            <p class="text-sm font-medium text-gray-400">
                No classes assigned yet.
            </p>
            <p class="text-xs text-gray-300 mt-1">
                Contact admin to get assigned.
            </p>
        </div>
    @endif
</div>

{{-- Recent Sessions --}}
<div class="mb-6">
    <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-1">
        Recent Activity
    </h2>

    @if ($recentSessions->isNotEmpty())
        <div class="space-y-2">
            @foreach ($recentSessions as $session)
                <div class="bg-white rounded-xl border border-gray-200 
                            p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 
                                flex items-center justify-center 
                                flex-shrink-0">
                        <i class="ti ti-calendar-event text-blue-500 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-700 truncate">
                            {{ $session->schoolClass->name }}
                            @if($session->subject)
                                · {{ $session->subject->name }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $session->session_date->format('M d, Y') }}
                            <span class="text-gray-300">·</span>
                            {{ $session->session_date->diffForHumans() }}
                        </p>
                    </div>
                    @if ($session->session_date->isToday())
                        <span class="text-[10px] font-semibold uppercase 
                                     tracking-wide px-2 py-1 rounded-lg
                                     bg-blue-50 text-blue-600 flex-shrink-0">
                            Today
                        </span>
                    @else
                        <span class="text-[10px] font-semibold uppercase 
                                     tracking-wide px-2 py-1 rounded-lg
                                     bg-green-50 text-green-600 flex-shrink-0">
                            Done
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 
                    px-5 py-8 text-center">
            <div class="w-14 h-14 rounded-full bg-gray-50 
                        flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-calendar-off text-2xl text-gray-300"></i>
            </div>
            <p class="text-sm font-medium text-gray-400">
                No recent sessions yet.
            </p>
            <p class="text-xs text-gray-300 mt-1">
                Start by taking attendance.
            </p>
        </div>
    @endif
</div>

@endsection