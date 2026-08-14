@extends('layouts.teacher', ['title' => 'Dashboard'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex items-center gap-3 mb-1">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-amber-400
                    flex items-center justify-center shadow-sm">
            <span class="text-lg font-bold text-white">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </span>
        </div>
        <div>
            <h1 class="text-lg font-bold text-gray-800">
                Welcome back, {{ auth()->user()->name }}
            </h1>
            <p class="text-xs text-gray-500">
                {{ now()->format('l, M d, Y') }}
            </p>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-3 gap-3 mb-6">

    {{-- Assigned Classes --}}
    <div class="bg-white/75 backdrop-blur-xl rounded-2xl border border-white/80 shadow-md shadow-green-900/10 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-green-50 ring-4 ring-green-100 
                    flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-building text-green-500 text-lg"></i>
        </div>
        <p class="text-xl font-bold text-gray-800">
            {{ $classes->count() }}
        </p>
        <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">
            Classes
        </p>
    </div>

    {{-- Total Students --}}
    <div class="bg-white/75 backdrop-blur-xl rounded-2xl border border-white/80 shadow-md shadow-green-900/10 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-amber-50 ring-4 ring-amber-100 
                    flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-users text-amber-500 text-lg"></i>
        </div>
        <p class="text-xl font-bold text-gray-800">
            {{ $classes->sum('active_students') }}
        </p>
        <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">
            Students
        </p>
    </div>

    {{-- Recent Sessions --}}
    <div class="bg-white/75 backdrop-blur-xl rounded-2xl border border-white/80 shadow-md shadow-green-900/10 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-purple-50 ring-4 ring-purple-100 
                    flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-calendar-check text-purple-500 text-lg"></i>
        </div>
        <p class="text-xl font-bold text-gray-800">
            {{ $recentSessions->count() }}
        </p>
        <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wide">
            Sessions
        </p>
    </div>

</div>

{{-- Quick Actions --}}
<div class="mb-6">
    <h2 class="text-xs font-bold text-gray-500 uppercase 
               tracking-wider mb-3 px-1">
        Quick Actions
    </h2>
    <div class="grid grid-cols-2 gap-3">

        <a href="{{ route('teacher.student-attendance.index') }}"
           class="bg-white/75 backdrop-blur-xl border border-white/80 rounded-2xl p-4 
                  text-center hover:bg-white/90 hover:shadow-lg 
                  shadow-md shadow-green-900/10
                  transition-all group active:scale-[0.98]">
            <div class="w-12 h-12 rounded-xl bg-green-50 
                        group-hover:bg-green-100 
                        flex items-center justify-center 
                        mx-auto mb-2 transition-colors">
                <i class="ti ti-calendar-check text-green-600 text-2xl"></i>
            </div>
            <span class="text-sm font-semibold text-gray-700">
                Take Attendance
            </span>
            <p class="text-[10px] text-gray-500 mt-0.5">
                Mark today's attendance
            </p>
        </a>

        <a href="{{ route('teacher.scores.index') }}"
           class="bg-white/75 backdrop-blur-xl border border-white/80 rounded-2xl p-4 
                  text-center hover:bg-white/90 hover:shadow-lg 
                  shadow-md shadow-green-900/10
                  transition-all group active:scale-[0.98]">
            <div class="w-12 h-12 rounded-xl bg-amber-50 
                        group-hover:bg-amber-100 
                        flex items-center justify-center 
                        mx-auto mb-2 transition-colors">
                <i class="ti ti-pencil text-amber-600 text-2xl"></i>
            </div>
            <span class="text-sm font-semibold text-gray-700">
                Enter Scores
            </span>
            <p class="text-[10px] text-gray-500 mt-0.5">
                Input exam scores
            </p>
        </a>

<a href="{{ route('teacher.reports.index') }}"
            class="bg-white/75 backdrop-blur-xl border border-white/80 rounded-2xl p-4
                text-center hover:bg-white/90 hover:shadow-lg
                shadow-md shadow-green-900/10
                  transition-all group active:scale-[0.98]">
            <div class="w-12 h-12 rounded-xl bg-purple-50 
                        group-hover:bg-purple-100 
                        flex items-center justify-center 
                        mx-auto mb-2 transition-colors">
                <i class="ti ti-chart-bar text-purple-600 text-2xl"></i>
            </div>
            <span class="text-sm font-semibold text-gray-700">
    Reports
</span>
<p class="text-[10px] text-gray-500 mt-0.5">
    View all reports
            </p>
        </a>

        <a href="{{ route('teacher.students.index') }}"
           class="bg-white/75 backdrop-blur-xl border border-white/80 rounded-2xl p-4 
                  text-center hover:bg-white/90 hover:shadow-lg 
                  shadow-md shadow-green-900/10
                  transition-all group active:scale-[0.98]">
            <div class="w-12 h-12 rounded-xl bg-yellow-50 
                        group-hover:bg-yellow-100 
                        flex items-center justify-center 
                        mx-auto mb-2 transition-colors">
                <i class="ti ti-users text-yellow-600 text-2xl"></i>
            </div>
            <span class="text-sm font-semibold text-gray-700">
                My Students
            </span>
            <p class="text-[10px] text-gray-500 mt-0.5">
                View student list
            </p>
        </a>

    </div>
</div>

{{-- My Classes --}}
<div class="mb-6">
    <h2 class="text-xs font-bold text-gray-500 uppercase 
               tracking-wider mb-3 px-1">
        My Classes
    </h2>

    @if ($classes->isNotEmpty())
        <div class="space-y-2">
            @foreach ($classes as $class)
                <div class="bg-white/75 backdrop-blur-xl rounded-2xl border border-white/80 
                            shadow-md shadow-green-900/10
                            p-4 flex items-center justify-between
                            hover:bg-white/90 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-green-50 
                                    flex items-center justify-center 
                                    flex-shrink-0">
                            <i class="ti ti-building text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $class->name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $class->grade->name }} · 
                                {{ $class->academicYear->name }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 
                                rounded-lg bg-white/80">
                        <i class="ti ti-users text-gray-500 text-sm"></i>
                        <span class="text-sm font-semibold text-gray-600">
                            {{ $class->active_students }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white/75 backdrop-blur-xl rounded-2xl border border-white/80 
                    shadow-md shadow-green-900/10
                    px-5 py-10 text-center">
            <div class="w-14 h-14 rounded-full bg-white/80 
                        flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-building-off text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-500">
                No classes assigned yet.
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Contact admin to get assigned.
            </p>
        </div>
    @endif
</div>

{{-- Recent Sessions --}}
<div class="mb-6">
    <h2 class="text-xs font-bold text-gray-500 uppercase 
               tracking-wider mb-3 px-1">
        Recent Activity
    </h2>

    @if ($recentSessions->isNotEmpty())
        <div class="space-y-2">
            @foreach ($recentSessions as $session)
                <div class="bg-white/75 backdrop-blur-xl rounded-2xl border border-white/80 
                            shadow-md shadow-green-900/10
                            p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 
                                flex items-center justify-center 
                                flex-shrink-0">
                        <i class="ti ti-calendar-event text-amber-500 text-lg"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-700 truncate">
                            {{ $session->schoolClass->name }}
                            @if($session->subject)
                                · {{ $session->subject->name }}
                            @endif
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $session->session_date->format('M d, Y') }}
                        </p>
                    </div>
                    <span class="text-[10px] font-semibold uppercase 
                                 tracking-wide px-2 py-1 rounded-lg
                                 bg-green-100 text-green-700 flex-shrink-0">
                        Done
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white/75 backdrop-blur-xl rounded-2xl border border-white/80 
                    shadow-md shadow-green-900/10
                    px-5 py-8 text-center">
            <div class="w-14 h-14 rounded-full bg-white/80 
                        flex items-center justify-center mx-auto mb-3">
                <i class="ti ti-calendar-off text-2xl text-gray-400"></i>
            </div>
            <p class="text-sm font-medium text-gray-500">
                No recent sessions yet.
            </p>
            <p class="text-xs text-gray-400 mt-1">
                Start by taking attendance.
            </p>
        </div>
    @endif
</div>

@endsection
