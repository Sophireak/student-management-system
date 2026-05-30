@extends('layouts.admin', ['title' => 'Class ' . $class->name . ' — Attendance'])

@section('content')

<div class="max-w-full">
    <a href="{{ route('admin.reports.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Reports
    </a>

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4
                flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-800">
                Class {{ $class->name }} — Attendance Report
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $class->grade->name }}
                · {{ $class->academicYear->name }}
            </p>
        </div>
        <a href="{{ route('admin.reports.class', $class) }}"
           class="text-xs px-3 py-1 bg-blue-100 text-blue-700
                  rounded hover:bg-blue-200">
            View Scores →
        </a>
    </div>

    @if ($sessionsBySubject->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    px-5 py-8 text-center text-gray-400 text-sm">
            No attendance sessions for this class.
        </div>
    @else

        {{-- Student summary table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-hidden mb-4">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">
                    Student Attendance Summary
                </h3>
            </div>
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 text-left">Student</th>
                        <th class="px-4 py-3 text-center">Total</th>
                        <th class="px-4 py-3 text-center">Present</th>
                        <th class="px-4 py-3 text-center">Absent</th>
                        <th class="px-4 py-3 text-center">Late</th>
                        <th class="px-4 py-3 text-center">Excused</th>
                        <th class="px-4 py-3 text-center">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($class->enrollments as $enrollment)
                        @php $summary = $studentSummary[$enrollment->id] @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800">
                                {{ $enrollment->student->full_name }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-500">
                                {{ $summary['total'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-green-700 font-medium">
                                {{ $summary['present'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-red-700 font-medium">
                                {{ $summary['absent'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-yellow-700 font-medium">
                                {{ $summary['late'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-blue-700 font-medium">
                                {{ $summary['excused'] }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="font-bold
                                    {{ $summary['rate'] >= 80
                                        ? 'text-green-700'
                                        : ($summary['rate'] >= 60
                                            ? 'text-yellow-700'
                                            : 'text-red-700') }}">
                                    {{ $summary['rate'] }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7"
                                class="px-4 py-6 text-center text-gray-400">
                                No students enrolled.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Detailed matrix by session --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-x-auto">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">
                    Session Detail
                </h3>
            </div>
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wide
                                   border-r border-gray-200 w-48 sticky
                                   left-0 bg-gray-50 z-10">
                            Student
                        </th>
                        @foreach ($sessionsBySubject as $subjectName => $sessions)
                            <th colspan="{{ $sessions->count() }}"
                                class="px-4 py-2 text-center text-xs font-semibold
                                       text-gray-600 uppercase tracking-wide
                                       border-r border-gray-200 bg-green-50">
                                {{ $subjectName }}
                            </th>
                        @endforeach
                    </tr>
                    <tr class="border-t border-gray-200">
                        <th class="px-4 py-2 border-r border-gray-200
                                   sticky left-0 bg-gray-50 z-10"></th>
                        @foreach ($sessionsBySubject as $sessions)
                            @foreach ($sessions as $session)
                                <th class="px-3 py-2 text-center text-xs
                                           text-gray-500 font-medium
                                           border-r border-gray-100 whitespace-nowrap">
                                    {{ $session->session_date->format('M d') }}
                                    @if ($session->period)
                                        <span class="block text-gray-400">
                                            {{ ucfirst($session->period) }}
                                        </span>
                                    @endif
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($class->enrollments as $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800
                                       border-r border-gray-200 whitespace-nowrap
                                       sticky left-0 bg-white z-10">
                                {{ $enrollment->student->full_name }}
                            </td>
                            @foreach ($sessionsBySubject as $sessions)
                                @foreach ($sessions as $session)
                                    <td class="px-3 py-3 text-center
                                               border-r border-gray-100">
                                        @if (isset($matrix[$enrollment->id][$session->id]))
                                            @php
                                                $att = $matrix[$enrollment->id][$session->id]
                                            @endphp
                                            <span class="text-xs font-semibold
                                                {{ match($att->status) {
                                                    'present' => 'text-green-600',
                                                    'absent'  => 'text-red-600',
                                                    'late'    => 'text-yellow-600',
                                                    'excused' => 'text-blue-600',
                                                } }}">
                                                {{ strtoupper(substr($att->status, 0, 1)) }}
                                            </span>
                                        @else
                                            <span class="text-gray-200 text-xs">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Legend --}}
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50
                        flex items-center gap-4 text-xs text-gray-500">
                <span class="font-semibold text-green-600">P</span> Present
                <span class="font-semibold text-red-600">A</span> Absent
                <span class="font-semibold text-yellow-600">L</span> Late
                <span class="font-semibold text-blue-600">E</span> Excused
            </div>
        </div>
    @endif
</div>

@endsection