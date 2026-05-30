@extends('layouts.admin', ['title' => 'Class ' . $class->name . ' — Report'])

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
                Class {{ $class->name }} — Score Report
            </h2>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $class->grade->name }}
                · {{ $class->academicYear->name }}
            </p>
        </div>
        <a href="{{ route('admin.reports.class.attendance', $class) }}"
           class="text-xs px-3 py-1 bg-green-100 text-green-700
                  rounded hover:bg-green-200">
            View Attendance →
        </a>
    </div>

    @if ($sessionsBySubject->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    px-5 py-8 text-center text-gray-400 text-sm">
            No exam sessions created for this class.
        </div>
    @else

        {{-- Per-session stat cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
            @foreach ($class->examSessions as $session)
                @php $stat = $sessionStats[$session->id] @endphp
                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <p class="text-sm font-semibold text-gray-700">
                            {{ $session->full_label }}
                        </p>
                        <span class="px-2 py-0.5 text-xs rounded capitalize
                            {{ match($session->type) {
                                'quiz'     => 'bg-purple-50 text-purple-600',
                                'monthly'  => 'bg-blue-50 text-blue-600',
                                'semester' => 'bg-yellow-50 text-yellow-600',
                                'final'    => 'bg-red-50 text-red-600',
                            } }}">
                            {{ $session->type }}
                        </span>
                    </div>
                    <p class="text-xs text-gray-400 mb-2">
                        {{ $session->subject->name }}
                        · Max: {{ $session->max_score }}
                    </p>
                    @if ($stat['count'] > 0)
                        <div class="grid grid-cols-3 gap-1 text-center text-xs">
                            <div class="bg-blue-50 rounded p-1">
                                <p class="font-bold text-blue-700">
                                    {{ $stat['average'] }}
                                </p>
                                <p class="text-blue-500">Avg</p>
                            </div>
                            <div class="bg-green-50 rounded p-1">
                                <p class="font-bold text-green-700">
                                    {{ $stat['highest'] }}
                                </p>
                                <p class="text-green-500">High</p>
                            </div>
                            <div class="bg-yellow-50 rounded p-1">
                                <p class="font-bold text-yellow-700">
                                    {{ $stat['lowest'] }}
                                </p>
                                <p class="text-yellow-500">Low</p>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-400">No scores yet.</p>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Score matrix --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wide
                                   border-r border-gray-200 w-48 sticky left-0
                                   bg-gray-50 z-10">
                            Student
                        </th>
                        @foreach ($sessionsBySubject as $subjectName => $sessions)
                            <th colspan="{{ $sessions->count() }}"
                                class="px-4 py-2 text-center text-xs font-semibold
                                       text-gray-600 uppercase tracking-wide
                                       border-r border-gray-200 bg-blue-50">
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
                                           border-r border-gray-100">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="whitespace-nowrap">
                                            {{ $session->name }}
                                        </span>
                                        <span class="text-gray-400">
                                            / {{ $session->max_score }}
                                        </span>
                                    </div>
                                </th>
                            @endforeach
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($class->enrollments as $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-800
                                       border-r border-gray-200 whitespace-nowrap
                                       sticky left-0 bg-white z-10">
                                <a href="{{ route('admin.reports.student', $enrollment->student) }}"
                                   class="hover:text-blue-600">
                                    {{ $enrollment->student->full_name }}
                                </a>
                            </td>
                            @foreach ($sessionsBySubject as $sessions)
                                @foreach ($sessions as $session)
                                    <td class="px-3 py-3 text-center
                                               border-r border-gray-100">
                                        @if (isset($matrix[$enrollment->id][$session->id]))
                                            @php
                                                $score = $matrix[$enrollment->id][$session->id]
                                            @endphp
                                            <span class="font-semibold text-gray-800">
                                                {{ $score->score }}
                                            </span>
                                            <span class="text-xs text-gray-400 block">
                                                {{ $score->percentage }}%
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="99"
                                class="px-4 py-6 text-center text-gray-400">
                                No students enrolled.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Average row --}}
                @if ($class->enrollments->isNotEmpty())
                    <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                        <tr>
                            <td class="px-4 py-3 text-xs font-semibold
                                       text-gray-500 uppercase border-r border-gray-200
                                       sticky left-0 bg-gray-50 z-10">
                                Class Average
                            </td>
                            @foreach ($sessionsBySubject as $sessions)
                                @foreach ($sessions as $session)
                                    <td class="px-3 py-3 text-center
                                               border-r border-gray-100 text-sm">
                                        @if ($sessionStats[$session->id]['average'] !== null)
                                            <span class="font-bold text-blue-700">
                                                {{ $sessionStats[$session->id]['average'] }}
                                            </span>
                                        @else
                                            <span class="text-gray-300 text-xs">—</span>
                                        @endif
                                    </td>
                                @endforeach
                            @endforeach
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    @endif
</div>

@endsection