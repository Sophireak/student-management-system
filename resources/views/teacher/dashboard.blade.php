@extends('layouts.admin', ['title' => 'Teacher Dashboard'])

@section('content')

{{-- Khmer-toned ambient background --}}
<div class="fixed inset-0 -z-10 bg-gradient-to-br from-green-50 via-amber-50/40 to-yellow-50 pointer-events-none"></div>

{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Welcome back, {{ auth()->user()->name }}</h1>
    <p class="text-sm text-gray-500 mt-1">Here's what's happening with your classes today.</p>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-building text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $classes->count() }}</p>
            <p class="text-sm text-gray-500">Assigned Classes</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-users text-blue-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $classes->sum('active_students') }}</p>
            <p class="text-sm text-gray-500">Total Students</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-calendar-check text-purple-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $recentSessions->count() }}</p>
            <p class="text-sm text-gray-500">Recent Sessions</p>
        </div>
    </div>

</div>

{{-- Quick Actions --}}
<div class="mb-6">
    <h2 class="text-sm font-semibold text-gray-700 mb-3">Quick Actions</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('teacher.student-attendance.index') }}"
           class="bg-white border border-gray-200 hover:border-green-300 hover:shadow-sm rounded-xl px-4 py-5 text-center transition-all">
            <i class="ti ti-calendar-check text-green-600 text-2xl block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Take Attendance</span>
        </a>
        <a href="{{ route('teacher.examination-scores.index') }}"
           class="bg-white border border-gray-200 hover:border-blue-300 hover:shadow-sm rounded-xl px-4 py-5 text-center transition-all">
            <i class="ti ti-clipboard-list text-blue-600 text-2xl block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Enter Scores</span>
        </a>
        <a href="{{ route('teacher.reports.ranking.index') }}"
           class="bg-white border border-gray-200 hover:border-purple-300 hover:shadow-sm rounded-xl px-4 py-5 text-center transition-all">
            <i class="ti ti-chart-bar text-purple-600 text-2xl block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Ranking Report</span>
        </a>
        <a href="{{ route('teacher.reports.honors.index') }}"
           class="bg-white border border-gray-200 hover:border-yellow-300 hover:shadow-sm rounded-xl px-4 py-5 text-center transition-all">
            <i class="ti ti-trophy text-yellow-600 text-2xl block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Honors Report</span>
        </a>
    </div>
</div>

{{-- My Classes --}}
<div class="mb-6">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-sm font-semibold text-gray-700">My Classes</h2>
    </div>
    @if ($classes->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-200 divide-y divide-gray-100">
            @foreach ($classes as $class)
                <div class="flex items-center justify-between px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                            <i class="ti ti-building text-green-600 text-base"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $class->name }}</p>
                            <p class="text-xs text-gray-400">{{ $class->grade->name }} · {{ $class->academicYear->name }}</p>
                        </div>
                    </div>
                    <span class="text-sm text-gray-500 flex items-center gap-1">
                        <i class="ti ti-users text-gray-400 text-sm"></i> {{ $class->active_students }}
                    </span>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-10 text-center">
            <i class="ti ti-building-off text-3xl text-gray-300 block mb-2"></i>
            <p class="text-gray-400 text-sm">No classes assigned yet.</p>
        </div>
    @endif
</div>
@endsection
