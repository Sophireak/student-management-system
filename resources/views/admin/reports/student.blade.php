@extends('layouts.admin', ['title' => $student->full_name . ' — Report Card'])

@section('content')

<div class="max-w-3xl">
    <a href="{{ route('admin.students.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Students
    </a>

    {{-- Student header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $student->full_name }}
                </h2>
                <p class="font-mono text-xs text-gray-400 mt-0.5">
                    {{ $student->student_id }}
                </p>
            </div>

            {{-- Enrollment selector --}}
            @if ($enrollments->count() > 1)
                <form method="GET"
                      action="{{ route('admin.reports.student', $student) }}">
                    <select name="enrollment_id"
                            onchange="this.form.submit()"
                            class="border border-gray-300 rounded-md px-3 py-1.5
                                   text-sm focus:outline-none focus:ring-2
                                   focus:ring-blue-500">
                        @foreach ($enrollments as $e)
                            <option value="{{ $e->id }}"
                                {{ $e->id === $selectedId ? 'selected' : '' }}>
                                {{ $e->schoolClass->name }}
                                — {{ $e->schoolClass->academicYear->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
            @elseif ($enrollments->count() === 1)
                <span class="text-sm text-gray-500">
                    {{ $enrollments->first()->schoolClass->name }}
                    — {{ $enrollments->first()->schoolClass->academicYear->name }}
                </span>
            @endif
        </div>
    </div>

    @if (! $report ?? true)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    px-5 py-8 text-center text-gray-400 text-sm">
            This student has no enrollment records.
        </div>
    @else

        {{-- Overall attendance summary --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
                Attendance Overview
            </h3>

            @if ($totalSessions > 0)
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 text-center text-sm">
                    <div class="bg-gray-50 rounded-md p-3 sm:col-span-1">
                        <p class="text-xl font-bold text-gray-800">
                            {{ $totalSessions }}
                        </p>
                        <p class="text-xs text-gray-500">Total Sessions</p>
                    </div>
                    <div class="bg-green-50 rounded-md p-3">
                        <p class="text-xl font-bold text-green-700">
                            {{ $overallPresent }}
                        </p>
                        <p class="text-xs text-green-600">Present</p>
                    </div>
                    <div class="bg-red-50 rounded-md p-3">
                        <p class="text-xl font-bold text-red-700">
                            {{ $overallAbsent }}
                        </p>
                        <p class="text-xs text-red-600">Absent</p>
                    </div>
                    <div class="bg-yellow-50 rounded-md p-3">
                        <p class="text-xl font-bold text-yellow-700">
                            {{ $overallLate }}
                        </p>
                        <p class="text-xs text-yellow-600">Late</p>
                    </div>
                    <div class="bg-blue-50 rounded-md p-3">
                        <p class="text-xl font-bold text-blue-700">
                            {{ $overallRate }}%
                        </p>
                        <p class="text-xs text-blue-600">Rate</p>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-400">No attendance records yet.</p>
            @endif
        </div>

        {{-- Score table by subject --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-hidden mb-4">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
                <h3 class="text-sm font-semibold text-gray-700">
                    Academic Performance
                </h3>
            </div>

            @forelse ($scoresBySubject as $subject)
                <div class="px-5 py-4 border-b border-gray-100 last:border-0">

                    {{-- Subject header --}}
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-semibold text-gray-700">
                            {{ $subject['subject'] }}
                        </p>
                        <div class="text-right text-xs text-gray-500">
                            Total:
                            <span class="font-semibold text-gray-700">
                                {{ $subject['total'] }}
                                / {{ $subject['max'] }}
                            </span>
                            <span class="ml-2">
                                Avg:
                                <span class="font-semibold text-blue-700">
                                    {{ $subject['average'] }}
                                </span>
                            </span>
                        </div>
                    </div>

                    {{-- Individual exam rows --}}
                    <div class="space-y-1.5">
                        @foreach ($subject['scores'] as $score)
                            <div class="flex items-center justify-between
                                        text-sm text-gray-600">
                                <div class="flex items-center gap-2">
                                    <span>
                                        {{ $score->examSession->full_label }}
                                    </span>
                                    <span class="px-1.5 py-0.5 text-xs rounded capitalize
                                        {{ match($score->examSession->type) {
                                            'quiz'     => 'bg-purple-50 text-purple-600',
                                            'monthly'  => 'bg-blue-50 text-blue-600',
                                            'semester' => 'bg-yellow-50 text-yellow-600',
                                            'final'    => 'bg-red-50 text-red-600',
                                        } }}">
                                        {{ $score->examSession->type }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <span class="font-semibold text-gray-800">
                                        {{ $score->score }}
                                    </span>
                                    <span class="text-gray-400 text-xs">
                                        / {{ $score->examSession->max_score }}
                                    </span>
                                    <span class="text-gray-400 text-xs ml-1">
                                        ({{ $score->percentage }}%)
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Attendance for this subject --}}
                    @if (isset($attendanceBySubject[$subject['subject']]))
                        @php $att = $attendanceBySubject[$subject['subject']] @endphp
                        <div class="mt-3 flex items-center gap-3 text-xs text-gray-400">
                            <span>Attendance:</span>
                            <span class="text-green-600">
                                {{ $att['present'] }} present
                            </span>
                            <span class="text-red-600">
                                {{ $att['absent'] }} absent
                            </span>
                            <span class="text-yellow-600">
                                {{ $att['late'] }} late
                            </span>
                            <span class="font-semibold text-blue-600">
                                {{ $att['rate'] }}% rate
                            </span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="px-5 py-6 text-center text-gray-400 text-sm">
                    No scores recorded for this enrollment.
                </div>
            @endforelse
        </div>

    @endif
</div>

@endsection