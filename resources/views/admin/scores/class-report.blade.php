@extends('layouts.admin', ['title' => 'Class ' . $class->name . ' — Scores'])

@section('content')

<div class="max-w-full">
    <a href="{{ route('admin.classes.show', $class) }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Class
    </a>

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
        <h2 class="text-lg font-bold text-gray-800">
            Class {{ $class->name }} — Score Report
        </h2>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ $class->grade->name }}
            · {{ $class->academicYear->name }}
        </p>
    </div>

    @if ($class->examSessions->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    px-5 py-8 text-center text-gray-400 text-sm">
            No exam sessions created for this class yet.
        </div>
    @else
        {{-- Group exam sessions by subject for column headers --}}
        @php
            $sessionsBySubject = $class->examSessions
                ->groupBy(fn($s) => $s->subject->name);
        @endphp

        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-gray-200">
                <thead class="bg-gray-50">

                    {{-- Subject header row --}}
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wide
                                   border-r border-gray-200 w-48">
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

                    {{-- Exam session column headers --}}
                    <tr class="border-t border-gray-200">
                        <th class="px-4 py-2 border-r border-gray-200"></th>
                        @foreach ($sessionsBySubject as $sessions)
                            @foreach ($sessions as $session)
                                <th class="px-3 py-2 text-center text-xs
                                           text-gray-500 font-medium
                                           border-r border-gray-100 whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span>{{ $session->name }}</span>
                                        <span class="px-1.5 py-0.5 rounded text-xs capitalize
                                            {{ match($session->type) {
                                                'quiz'     => 'bg-purple-50 text-purple-600',
                                                'monthly'  => 'bg-blue-50 text-blue-600',
                                                'semester' => 'bg-yellow-50 text-yellow-600',
                                                'final'    => 'bg-red-50 text-red-600',
                                            } }}">
                                            {{ $session->type }}
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
                                       border-r border-gray-200 whitespace-nowrap">
                                {{ $enrollment->student->full_name }}
                            </td>

                            @foreach ($sessionsBySubject as $sessions)
                                @foreach ($sessions as $session)
                                    <td class="px-3 py-3 text-center
                                               border-r border-gray-100">
                                        @if (isset($matrix[$enrollment->id][$session->id]))
                                            @php $score = $matrix[$enrollment->id][$session->id] @endphp
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
                                No students enrolled in this class.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- Average row --}}
                @if ($class->enrollments->isNotEmpty())
                    <tfoot class="bg-gray-50 border-t border-gray-200">
                        <tr>
                            <td class="px-4 py-3 text-xs font-semibold
                                       text-gray-500 uppercase border-r border-gray-200">
                                Average
                            </td>
                            @foreach ($sessionsBySubject as $sessions)
                                @foreach ($sessions as $session)
                                    @php
                                        $sessionScores = collect($matrix)
                                            ->map(fn($row) =>
                                                isset($row[$session->id])
                                                    ? $row[$session->id]->score
                                                    : null
                                            )
                                            ->filter()
                                            ->values();

                                        $avg = $sessionScores->isNotEmpty()
                                            ? round($sessionScores->avg(), 1)
                                            : null;
                                    @endphp
                                    <td class="px-3 py-3 text-center
                                               border-r border-gray-100">
                                        @if ($avg !== null)
                                            <span class="font-semibold text-blue-700">
                                                {{ $avg }}
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