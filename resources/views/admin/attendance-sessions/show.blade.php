@extends('layouts.admin', ['title' => 'Attendance Session'])

@section('content')

<div class="max-w-3xl">
    <a href="{{ route('admin.attendance-sessions.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Sessions
    </a>

    {{-- Session header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $attendanceSession->schoolClass->name }}
                    — {{ $attendanceSession->subject->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $attendanceSession->session_date->format('l, M d, Y') }}
                    @if ($attendanceSession->period)
                        · {{ ucfirst($attendanceSession->period) }}
                    @endif
                    · {{ $attendanceSession->schoolClass->grade->name }}
                    · {{ $attendanceSession->schoolClass->academicYear->name }}
                </p>
                @if ($attendanceSession->topic)
                    <p class="text-sm text-gray-400 mt-1">
                        Topic: {{ $attendanceSession->topic }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Summary counts --}}
        @php
            $att      = $attendanceSession->attendances;
            $present  = $att->where('status', 'present')->count();
            $absent   = $att->where('status', 'absent')->count();
            $late     = $att->where('status', 'late')->count();
            $excused  = $att->where('status', 'excused')->count();
            $total    = $att->count();
        @endphp

        @if ($total > 0)
            <div class="grid grid-cols-4 gap-3 mt-4 text-center text-sm">
                <div class="bg-green-50 rounded-md p-3">
                    <p class="text-xl font-bold text-green-700">{{ $present }}</p>
                    <p class="text-xs text-green-600">Present</p>
                </div>
                <div class="bg-red-50 rounded-md p-3">
                    <p class="text-xl font-bold text-red-700">{{ $absent }}</p>
                    <p class="text-xs text-red-600">Absent</p>
                </div>
                <div class="bg-yellow-50 rounded-md p-3">
                    <p class="text-xl font-bold text-yellow-700">{{ $late }}</p>
                    <p class="text-xs text-yellow-600">Late</p>
                </div>
                <div class="bg-blue-50 rounded-md p-3">
                    <p class="text-xl font-bold text-blue-700">{{ $excused }}</p>
                    <p class="text-xs text-blue-600">Excused</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Attendance records --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-700">Student Attendance</h3>
        </div>

        @forelse ($attendanceSession->attendances as $record)
            <div class="flex items-center justify-between px-5 py-3
                        border-b border-gray-100 last:border-0 text-sm">
                <div>
                    <p class="font-medium text-gray-800">
                        {{ $record->enrollment->student->full_name }}
                    </p>
                    @if ($record->notes)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $record->notes }}</p>
                    @endif
                </div>
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ match($record->status) {
                        'present' => 'bg-green-100 text-green-700',
                        'absent'  => 'bg-red-100 text-red-700',
                        'late'    => 'bg-yellow-100 text-yellow-700',
                        'excused' => 'bg-blue-100 text-blue-700',
                    } }}">
                    {{ ucfirst($record->status) }}
                </span>
            </div>
        @empty
            <div class="px-5 py-6 text-center text-gray-400 text-sm">
                No attendance marked yet.
            </div>
        @endforelse
    </div>
</div>

@endsection