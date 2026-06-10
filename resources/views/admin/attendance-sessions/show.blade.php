@extends('layouts.admin', ['title' => 'Attendance Session'])

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.attendance-sessions.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Sessions
    </a>
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-calendar-check text-green-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $session->session_date->format('M d, Y') }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">
                    {{ $session->schoolClass->name }} · {{ $session->subject->name }}
                    @if ($session->period)
                        · <span class="capitalize">{{ $session->period }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Session Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Session Info</h2>

        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-calendar text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Date</p>
                    <p class="text-sm font-medium text-gray-700">{{ $session->session_date->format('M d, Y') }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-building text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Class</p>
                    <p class="text-sm font-medium text-gray-700">{{ $session->schoolClass->name }}</p>
                    <p class="text-xs text-gray-400">{{ $session->schoolClass->grade->name }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-book text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Subject</p>
                    <p class="text-sm font-medium text-gray-700">{{ $session->subject->name }}</p>
                </div>
            </div>
            @if ($session->period)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-clock text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Period</p>
                    <p class="text-sm font-medium text-gray-700 capitalize">{{ $session->period }}</p>
                </div>
            </div>
            @endif
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-users text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Total Marked</p>
                    <p class="text-sm font-medium text-gray-700">{{ $session->attendances->count() }} students</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Attendance List --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Attendance Records</h2>

        <form method="POST" action="{{ route('admin.attendance-sessions.attendance.index', $session) }}">
            @csrf

            @if ($session->attendances->count() > 0)
                <div class="space-y-1 mb-4">
                    @foreach ($session->attendances()->with('student')->get() as $attendance)
                        <div class="flex items-center justify-between py-2.5 px-3 rounded-lg hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <i class="ti ti-user text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $attendance->student->full_name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $attendance->student->student_id }}</p>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                                {{ $attendance->status === 'present' ? 'bg-green-100 text-green-700' :
                                   ($attendance->status === 'absent'  ? 'bg-red-100 text-red-700' :
                                    'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($attendance->status) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-10 text-center">
                    <i class="ti ti-clipboard-off text-4xl text-gray-300 block mb-2"></i>
                    <p class="text-sm text-gray-400">No attendance records yet.</p>
                </div>
            @endif

        </form>
    </div>

</div>

@endsection
