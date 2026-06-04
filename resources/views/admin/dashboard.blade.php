@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">Welcome back — here's what's happening today.</p>
</div>

{{-- Active Academic Year Banner --}}
@if ($activeYear)
<div class="bg-green-50 border border-green-200 rounded-xl px-5 py-4 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <i class="ti ti-calendar text-green-600 text-xl"></i>
        <div>
            <p class="text-xs font-semibold text-green-600 uppercase tracking-wide">Active Academic Year</p>
            <p class="text-base font-bold text-green-800">{{ $activeYear->name }}</p>
        </div>
    </div>
    <span class="text-sm text-green-600 hidden sm:block">
        {{ $activeYear->start_date->format('M d, Y') }} — {{ $activeYear->end_date->format('M d, Y') }}
    </span>
</div>
@else
<div class="bg-yellow-50 border border-yellow-200 rounded-xl px-5 py-4 mb-6 flex items-center gap-3">
    <i class="ti ti-alert-triangle text-yellow-500 text-xl"></i>
    <p class="text-sm font-medium text-yellow-700">
        No active academic year is set.
        <a href="{{ route('admin.academic-years.index') }}" class="underline font-semibold">Set one now</a>.
    </p>
</div>
@endif

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-admin.stat-card label="Students"    value="{{ $totalStudents }}"    icon="ti ti-users"          color="blue" />
    <x-admin.stat-card label="Teachers"    value="{{ $totalTeachers }}"    icon="ti ti-school"         color="green" />
    <x-admin.stat-card label="Classes"     value="{{ $totalClasses }}"     icon="ti ti-building"       color="purple" />
    <x-admin.stat-card label="Enrollments" value="{{ $totalEnrollments }}" icon="ti ti-clipboard-list" color="yellow" />
</div>

{{-- Main Content Row --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Quick Actions</h2>
        <div class="space-y-1">
            <a href="{{ route('admin.students.create') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 text-sm text-gray-700 font-medium transition-colors group">
                <div class="flex items-center gap-3">
                    <i class="ti ti-user-plus text-gray-400 group-hover:text-green-600 text-base"></i>
                    Add New Student
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>
            <a href="{{ route('admin.teachers.create') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 text-sm text-gray-700 font-medium transition-colors group">
                <div class="flex items-center gap-3">
                    <i class="ti ti-user-check text-gray-400 group-hover:text-green-600 text-base"></i>
                    Add New Teacher
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>
            <a href="{{ route('admin.enrollments.create') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 text-sm text-gray-700 font-medium transition-colors group">
                <div class="flex items-center gap-3">
                    <i class="ti ti-clipboard-plus text-gray-400 group-hover:text-green-600 text-base"></i>
                    Enroll Student
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>
            <a href="{{ route('admin.classes.create') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 text-sm text-gray-700 font-medium transition-colors group">
                <div class="flex items-center gap-3">
                    <i class="ti ti-building-plus text-gray-400 group-hover:text-green-600 text-base"></i>
                    Create Class
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>
            <a href="{{ route('admin.attendance-sessions.index') }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 text-sm text-gray-700 font-medium transition-colors group">
                <div class="flex items-center gap-3">
                    <i class="ti ti-calendar-check text-gray-400 group-hover:text-green-600 text-base"></i>
                    View Attendance
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>
        </div>
    </div>

    {{-- System Overview --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">System Overview</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">

            <a href="{{ route('admin.students.index') }}"
               class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 group-hover:bg-green-100 flex items-center justify-center">
                        <i class="ti ti-users text-green-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Students</p>
                        <p class="text-xs text-gray-400">Manage all students</p>
                    </div>
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>

            <a href="{{ route('admin.teachers.index') }}"
               class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 group-hover:bg-green-100 flex items-center justify-center">
                        <i class="ti ti-school text-green-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Teachers</p>
                        <p class="text-xs text-gray-400">Manage all teachers</p>
                    </div>
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>

            <a href="{{ route('admin.classes.index') }}"
               class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 group-hover:bg-green-100 flex items-center justify-center">
                        <i class="ti ti-building text-green-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Classes</p>
                        <p class="text-xs text-gray-400">Manage all classes</p>
                    </div>
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>

            <a href="{{ route('admin.enrollments.index') }}"
               class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 group-hover:bg-green-100 flex items-center justify-center">
                        <i class="ti ti-clipboard-list text-green-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Enrollments</p>
                        <p class="text-xs text-gray-400">Manage enrollments</p>
                    </div>
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>

            <a href="{{ route('admin.examination-scores.index') }}"
               class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 group-hover:bg-green-100 flex items-center justify-center">
                        <i class="ti ti-chart-bar text-green-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Examination Scores</p>
                        <p class="text-xs text-gray-400">View & enter scores</p>
                    </div>
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>

            <a href="{{ route('admin.score-report.index') }}"
               class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-green-200 hover:bg-green-50 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 group-hover:bg-green-100 flex items-center justify-center">
                        <i class="ti ti-file-analytics text-green-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-700">Score Reports</p>
                        <p class="text-xs text-gray-400">View score results</p>
                    </div>
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>

        </div>
    </div>

</div>

@endsection
