@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')

{{-- =============================================
     GREETING + DATE + ACTIVE YEAR
     ============================================= --}}
<div x-data="{
    hour: new Date().getHours(),
    get greeting() {
        return this.hour < 12 
            ? 'Good Morning' 
            : this.hour < 18 
                ? 'Good Afternoon' 
                : 'Good Evening'
    }
}" class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center 
                sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                <span x-text="greeting"></span>, 
                {{ auth()->user()->name }} 👋
            </h1>
            <div class="flex items-center gap-2 mt-1 flex-wrap">
                <span class="text-sm text-gray-400">
                    {{ now()->format('l, M d, Y') }}
                </span>
                <span class="text-gray-300">·</span>
                @if ($activeYear)
                    <span class="inline-flex items-center gap-1 
                                 text-xs font-semibold text-green-600">
                        <i class="ti ti-circle-check text-green-500"></i>
                        {{ $activeYear->name }}
                    </span>
                @else
                    <a href="{{ route('admin.academic-years.index') }}"
                       class="inline-flex items-center gap-1 
                              text-xs font-semibold text-red-500 
                              hover:text-red-600 transition-colors">
                        <i class="ti ti-alert-circle"></i>
                        No active year — Fix now
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- =============================================
     METRIC CARDS
     ============================================= --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    {{-- Students --}}
    <a href="{{ route('admin.students.index') }}"
       class="group relative bg-white rounded-2xl border border-gray-200 
              p-5 hover:border-blue-200 hover:shadow-lg 
              hover:shadow-blue-500/10 transition-all duration-300 
              overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full 
                    bg-gradient-to-b from-blue-400 to-blue-600 
                    rounded-l-2xl">
        </div>
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 
                        flex items-center justify-center 
                        group-hover:scale-110 transition-transform">
                <i class="ti ti-users text-blue-500 text-xl"></i>
            </div>
            @if ($newStudentsThisMonth > 0)
                <span class="text-[10px] font-bold px-2 py-0.5 
                             rounded-full bg-blue-50 text-blue-600">
                    +{{ $newStudentsThisMonth }} new
                </span>
            @endif
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ number_format($totalStudents) }}
        </p>
        <p class="text-xs text-gray-400 mt-1">Total Students</p>
    </a>

    {{-- Teachers --}}
    <a href="{{ route('admin.teachers.index') }}"
       class="group relative bg-white rounded-2xl border border-gray-200 
              p-5 hover:border-green-200 hover:shadow-lg 
              hover:shadow-green-500/10 transition-all duration-300 
              overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full 
                    bg-gradient-to-b from-green-400 to-green-600 
                    rounded-l-2xl">
        </div>
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-50 
                        flex items-center justify-center 
                        group-hover:scale-110 transition-transform">
                <i class="ti ti-school text-green-500 text-xl"></i>
            </div>
            <span class="text-xs font-medium text-gray-400">Total</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ number_format($totalTeachers) }}
        </p>
        <p class="text-xs text-gray-400 mt-1">Total Teachers</p>
    </a>

    {{-- Classes --}}
    <a href="{{ route('admin.classes.index') }}"
       class="group relative bg-white rounded-2xl border border-gray-200 
              p-5 hover:border-purple-200 hover:shadow-lg 
              hover:shadow-purple-500/10 transition-all duration-300 
              overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full 
                    bg-gradient-to-b from-purple-400 to-purple-600 
                    rounded-l-2xl">
        </div>
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-purple-50 
                        flex items-center justify-center 
                        group-hover:scale-110 transition-transform">
                <i class="ti ti-building text-purple-500 text-xl"></i>
            </div>
            <span class="text-xs font-medium text-gray-400">Total</span>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ number_format($totalClasses) }}
        </p>
        <p class="text-xs text-gray-400 mt-1">Total Classes</p>
    </a>

    {{-- Enrollments --}}
    <a href="{{ route('admin.enrollments.index') }}"
       class="group relative bg-white rounded-2xl border border-gray-200 
              p-5 hover:border-amber-200 hover:shadow-lg 
              hover:shadow-amber-500/10 transition-all duration-300 
              overflow-hidden">
        <div class="absolute top-0 left-0 w-1 h-full 
                    bg-gradient-to-b from-amber-400 to-amber-600 
                    rounded-l-2xl">
        </div>
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 
                        flex items-center justify-center 
                        group-hover:scale-110 transition-transform">
                <i class="ti ti-clipboard-list text-amber-500 text-xl"></i>
            </div>
            @if ($newEnrollmentsThisMonth > 0)
                <span class="text-[10px] font-bold px-2 py-0.5 
                             rounded-full bg-amber-50 text-amber-600">
                    +{{ $newEnrollmentsThisMonth }} new
                </span>
            @endif
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ number_format($totalEnrollments) }}
        </p>
        <p class="text-xs text-gray-400 mt-1">Active Enrollments</p>
    </a>

</div>

{{-- =============================================
     TODAY'S ATTENDANCE BAR
     ============================================= --}}
<div class="bg-white rounded-2xl border border-gray-200 p-5 mb-6">

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-lg bg-green-50 
                        flex items-center justify-center">
                <i class="ti ti-chart-bar text-green-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">
                Today's Attendance
            </h2>
        </div>
        <a href="{{ route('admin.student-attendance.index') }}"
           class="text-xs font-semibold text-green-600 
                  hover:text-green-700 transition-colors">
            Manage →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-center">

        {{-- Progress Bar --}}
        <div class="sm:col-span-2">
            <div class="flex items-center justify-between mb-1.5">
                <span class="text-xs text-gray-500">
                    Attendance Rate
                </span>
                <span class="text-xs font-bold
                             {{ $todayAttendance >= 80 
                                 ? 'text-green-600' 
                                 : ($todayAttendance >= 60 
                                     ? 'text-amber-600' 
                                     : 'text-red-500') }}">
                    {{ $todayTotal > 0 ? $todayAttendance . '%' : 'No data yet' }}
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-700
                            {{ $todayAttendance >= 80 
                                ? 'bg-gradient-to-r from-green-400 to-green-500' 
                                : ($todayAttendance >= 60 
                                    ? 'bg-gradient-to-r from-amber-400 to-amber-500' 
                                    : 'bg-gradient-to-r from-red-400 to-red-500') }}"
                     style="width: {{ $todayTotal > 0 ? $todayAttendance : 0 }}%">
                </div>
            </div>
            <p class="text-[10px] text-gray-400 mt-1.5">
                {{ now()->format('F') }} monthly rate: 
                <span class="font-semibold text-gray-500">
                    {{ $monthAttendance }}%
                </span>
            </p>
        </div>

        {{-- Sessions Count --}}
        <div class="flex sm:flex-col items-center sm:items-end 
                    gap-3 sm:gap-1">
            <div class="text-right">
                <p class="text-2xl font-extrabold text-gray-800">
                    {{ $sessionsToday }}
                </p>
                <p class="text-xs text-gray-400">
                    session{{ $sessionsToday !== 1 ? 's' : '' }} today
                </p>
            </div>
            @if ($sessionsToday === 0)
                <a href="{{ route('admin.student-attendance.index') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 
                          bg-amber-50 border border-amber-200 
                          text-amber-600 text-xs font-bold 
                          rounded-lg hover:bg-amber-100 transition-colors">
                    <i class="ti ti-clock-exclamation text-xs"></i>
                    Take Now
                </a>
            @else
                <span class="inline-flex items-center gap-1 
                             px-2.5 py-1 rounded-lg 
                             bg-green-50 border border-green-200 
                             text-green-600 text-xs font-bold">
                    <i class="ti ti-circle-check text-xs"></i>
                    Done
                </span>
            @endif
        </div>

    </div>
</div>

{{-- =============================================
     ALERTS — Only shows when there's a problem
     ============================================= --}}
@php
    $hasAlerts = !$activeYear 
        || $totalStudents === 0 
        || $totalTeachers === 0 
        || $totalClasses === 0;
@endphp

@if ($hasAlerts)
    <div class="space-y-3">
        <h2 class="text-xs font-bold text-gray-400 uppercase tracking-wider px-1">
            Needs Attention
        </h2>

        {{-- No Active Year --}}
        @if (!$activeYear)
            <div class="flex items-center gap-4 p-4 
                        bg-red-50 border border-red-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-red-100 
                            flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-alert-circle text-red-500 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-red-700">
                        No Active Academic Year
                    </p>
                    <p class="text-xs text-red-400 mt-0.5">
                        Set an active year so the system works correctly.
                    </p>
                </div>
                <a href="{{ route('admin.academic-years.index') }}"
                   class="flex-shrink-0 px-3 py-1.5 bg-red-100 
                          hover:bg-red-200 text-red-600 text-xs 
                          font-bold rounded-lg transition-colors">
                    Fix →
                </a>
            </div>
        @endif

        {{-- No Students --}}
        @if ($totalStudents === 0)
            <div class="flex items-center gap-4 p-4 
                        bg-amber-50 border border-amber-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-amber-100 
                            flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-users text-amber-500 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-amber-700">
                        No Students Registered
                    </p>
                    <p class="text-xs text-amber-400 mt-0.5">
                        Add your first student to get started.
                    </p>
                </div>
                <a href="{{ route('admin.students.create') }}"
                   class="flex-shrink-0 px-3 py-1.5 bg-amber-100 
                          hover:bg-amber-200 text-amber-600 text-xs 
                          font-bold rounded-lg transition-colors">
                    Add →
                </a>
            </div>
        @endif

        {{-- No Teachers --}}
        @if ($totalTeachers === 0)
            <div class="flex items-center gap-4 p-4 
                        bg-amber-50 border border-amber-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-amber-100 
                            flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-school text-amber-500 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-amber-700">
                        No Teachers Registered
                    </p>
                    <p class="text-xs text-amber-400 mt-0.5">
                        Add teachers so they can manage their classes.
                    </p>
                </div>
                <a href="{{ route('admin.teachers.create') }}"
                   class="flex-shrink-0 px-3 py-1.5 bg-amber-100 
                          hover:bg-amber-200 text-amber-600 text-xs 
                          font-bold rounded-lg transition-colors">
                    Add →
                </a>
            </div>
        @endif

        {{-- No Classes --}}
        @if ($totalClasses === 0)
            <div class="flex items-center gap-4 p-4 
                        bg-amber-50 border border-amber-200 rounded-2xl">
                <div class="w-10 h-10 rounded-xl bg-amber-100 
                            flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-building text-amber-500 text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-amber-700">
                        No Classes Created
                    </p>
                    <p class="text-xs text-amber-400 mt-0.5">
                        Create classes before enrolling students.
                    </p>
                </div>
                <a href="{{ route('admin.classes.create') }}"
                   class="flex-shrink-0 px-3 py-1.5 bg-amber-100 
                          hover:bg-amber-200 text-amber-600 text-xs 
                          font-bold rounded-lg transition-colors">
                    Create →
                </a>
            </div>
        @endif

    </div>
@endif

@endsection