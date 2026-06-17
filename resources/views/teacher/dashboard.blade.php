@extends('layouts.admin', ['title' => 'Teacher Dashboard'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Welcome, {{ auth()->user()->name }}</h1>
    <p class="text-sm text-gray-500 mt-1">Here's an overview of your assigned classes.</p>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-building text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $classes->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Assigned Classes</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-users text-blue-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $classes->sum('active_students') }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Active Students</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-calendar-check text-purple-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-800">{{ $recentSessions->count() }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Recent Sessions</p>
        </div>
    </div>

</div>

{{-- Classes --}}
@if ($classes->isNotEmpty())
    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">My Classes</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @foreach ($classes as $class)
            <div class="bg-white rounded-xl border border-gray-200 p-5">

                {{-- Class Info --}}
                <div class="flex items-start gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-building text-green-600 text-lg"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ $class->name }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $class->grade->name }} · {{ $class->academicYear->name }}</p>
                    </div>
                </div>

                {{-- Student Count --}}
                <div class="flex items-center gap-2 mb-4 px-3 py-2 bg-gray-50 rounded-lg">
                    <i class="ti ti-users text-gray-400 text-sm"></i>
                    <span class="text-sm text-gray-500"><span class="font-semibold text-gray-700">{{ $class->active_students }}</span> active students</span>
                </div>

                {{-- Quick Actions --}}
                <div class="flex items-center gap-2">
                    <a href="{{ route('teacher.student-attendance.index') }}"
                       class="flex-1 flex items-center justify-center gap-1.5 text-xs px-2 py-2 bg-green-50 border border-green-200 text-green-700 rounded-lg hover:bg-green-100 transition-colors font-medium">
                        <i class="ti ti-calendar-check text-sm"></i> Attendance
                    </a>
                    <a href="{{ route('teacher.examination-scores.index') }}"
                       class="flex-1 flex items-center justify-center gap-1.5 text-xs px-2 py-2 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors font-medium">
                        <i class="ti ti-clipboard-list text-sm"></i> Scores
                    </a>
                    <a href="{{ route('teacher.reports.ranking.index') }}"
                       class="flex-1 flex items-center justify-center gap-1.5 text-xs px-2 py-2 bg-purple-50 border border-purple-200 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors font-medium">
                        <i class="ti ti-chart-bar text-sm"></i> Reports
                    </a>
                </div>

            </div>
        @endforeach
    </div>
@else
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center mb-6">
        <i class="ti ti-building-off text-4xl text-gray-300 block mb-2"></i>
        <p class="text-gray-400 text-sm">No classes assigned for the active academic year.</p>
        <p class="text-xs text-gray-400 mt-1">Contact your administrator.</p>
    </div>
@endif

{{-- Recent Attendance Sessions --}}
<h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Recent Attendance Sessions</h2>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    @if ($recentSessions->isNotEmpty())
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Class</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($recentSessions as $session)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <i class="ti ti-calendar text-green-600 text-sm"></i>
                                </div>
                                <span class="font-medium text-gray-800">{{ $session->session_date->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $session->schoolClass->name }}
                            <span class="text-gray-400 text-xs ml-1">· {{ $session->schoolClass->grade->name }}</span>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $session->subject->name }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('teacher.attendance-sessions.show', $session) }}"
                               class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors inline-flex"
                               title="View">
                                <i class="ti ti-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="px-5 py-12 text-center">
            <i class="ti ti-calendar-off text-4xl text-gray-300 block mb-2"></i>
            <p class="text-gray-400 text-sm">No attendance sessions recorded yet.</p>
        </div>
    @endif
</div>

@endsection
