@extends('layouts.admin', ['title' => 'Enrollment Detail'])

@section('content')

<div class="max-w-2xl">
    <a href="{{ route('admin.enrollments.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Enrollments
    </a>

    {{-- Summary --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $enrollment->student->full_name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $enrollment->schoolClass->name }}
                    · {{ $enrollment->schoolClass->grade->name }}
                    · {{ $enrollment->schoolClass->academicYear->name }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-1 text-xs font-semibold rounded-full
                    {{ match($enrollment->status) {
                        'active'      => 'bg-green-100 text-green-700',
                        'transferred' => 'bg-yellow-100 text-yellow-700',
                        'dropped'     => 'bg-red-100 text-red-700',
                    } }}">
                    {{ ucfirst($enrollment->status) }}
                </span>
                <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                   class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                    Change Status
                </a>
            </div>
        </div>

        <p class="text-sm text-gray-500">
            Enrolled on:
            <span class="font-medium text-gray-700">
                {{ $enrollment->enrolled_at->format('M d, Y') }}
            </span>
        </p>
    </div>

    {{-- Scores --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Scores</h3>

        @forelse ($enrollment->scores as $score)
            <div class="flex items-center justify-between py-2 border-b border-gray-100
                        last:border-0 text-sm">
                <div>
                    <span class="font-medium text-gray-800">
                        {{ $score->examSession->name }}
                    </span>
                    <span class="text-gray-400 mx-1">·</span>
                    <span class="text-gray-500">
                        {{ $score->examSession->subject->name }}
                    </span>
                </div>
                <span class="font-bold text-gray-800">
                    {{ $score->score }}
                    <span class="text-xs text-gray-400 font-normal">
                        / {{ $score->examSession->max_score }}
                    </span>
                </span>
            </div>
        @empty
            <p class="text-sm text-gray-400">No scores recorded yet.</p>
        @endforelse
    </div>

    {{-- Attendance summary --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Attendance</h3>

        @php
            $total    = $enrollment->attendances->count();
            $present  = $enrollment->attendances->where('status', 'present')->count();
            $absent   = $enrollment->attendances->where('status', 'absent')->count();
            $late     = $enrollment->attendances->where('status', 'late')->count();
            $excused  = $enrollment->attendances->where('status', 'excused')->count();
        @endphp

        @if ($total > 0)
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm text-center">
                <div class="p-3 bg-green-50 rounded-md">
                    <p class="text-xl font-bold text-green-700">{{ $present }}</p>
                    <p class="text-xs text-green-600">Present</p>
                </div>
                <div class="p-3 bg-red-50 rounded-md">
                    <p class="text-xl font-bold text-red-700">{{ $absent }}</p>
                    <p class="text-xs text-red-600">Absent</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-md">
                    <p class="text-xl font-bold text-yellow-700">{{ $late }}</p>
                    <p class="text-xs text-yellow-600">Late</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-md">
                    <p class="text-xl font-bold text-blue-700">{{ $excused }}</p>
                    <p class="text-xs text-blue-600">Excused</p>
                </div>
            </div>
        @else
            <p class="text-sm text-gray-400">No attendance records yet.</p>
        @endif
    </div>

</div>

@endsection