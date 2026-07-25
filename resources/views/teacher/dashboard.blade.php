@extends('layouts.admin', ['title' => 'Teacher Dashboard'])

@section('content')

{{-- Page Header --}}
<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
            <i class="ti ti-layout-dashboard text-green-600 text-xl"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-gray-800 leading-tight">Welcome back, {{ auth()->user()->name }}</h1>
            <span class="inline-flex items-center gap-1 mt-0.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
                <i class="ti ti-sparkles text-sm"></i> Here's what's happening with your classes today
            </span>
        </div>
    </div>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">

    <div class="bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-building text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $classes->count() }}</p>
            <p class="text-sm text-gray-500">Assigned Classes</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-users text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $classes->sum('active_students') }}</p>
            <p class="text-sm text-gray-500">Total Students</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-5 flex items-center gap-4 shadow-sm">
        <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-calendar-check text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $recentSessions->count() }}</p>
            <p class="text-sm text-gray-500">Recent Sessions</p>
        </div>
    </div>

</div>

{{-- Quick Actions --}}
<div class="bg-white rounded-2xl border border-gray-200 p-5 mb-5 shadow-sm">
    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Quick Actions</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="{{ route('teacher.student-attendance.index') }}"
           class="bg-gray-50 border border-gray-200 hover:border-green-300 hover:bg-green-50 rounded-xl px-4 py-5 text-center transition-all">
            <i class="ti ti-calendar-check text-green-600 text-2xl block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Take Attendance</span>
        </a>
        <a href="{{ route('teacher.examination-scores.index') }}"
           class="bg-gray-50 border border-gray-200 hover:border-green-300 hover:bg-green-50 rounded-xl px-4 py-5 text-center transition-all">
            <i class="ti ti-clipboard-list text-green-600 text-2xl block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Enter Scores</span>
        </a>
        <a href="{{ route('teacher.reports.ranking.index') }}"
           class="bg-gray-50 border border-gray-200 hover:border-green-300 hover:bg-green-50 rounded-xl px-4 py-5 text-center transition-all">
            <i class="ti ti-chart-bar text-green-600 text-2xl block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Ranking Report</span>
        </a>
        <a href="{{ route('teacher.reports.honors.index') }}"
           class="bg-gray-50 border border-gray-200 hover:border-green-300 hover:bg-green-50 rounded-xl px-4 py-5 text-center transition-all">
            <i class="ti ti-trophy text-green-600 text-2xl block mb-2"></i>
            <span class="text-sm font-medium text-gray-700">Honors Report</span>
        </a>
    </div>
</div>

{{-- My Classes --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">My Classes</h2>
        <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
            <i class="ti ti-building text-sm"></i> {{ $classes->count() }} total
        </span>
    </div>
    @if ($classes->isNotEmpty())
        <div class="divide-y divide-gray-100">
            @foreach ($classes as $class)
                <div class="flex items-center justify-between px-5 py-4 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
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
        <div class="px-5 py-10 text-center">
            <i class="ti ti-building-off text-3xl text-gray-300 block mb-2"></i>
            <p class="text-gray-400 text-sm">No classes assigned yet.</p>
        </div>
    @endif
</div>
@endsection
