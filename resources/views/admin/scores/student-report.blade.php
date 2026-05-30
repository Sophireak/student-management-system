@extends('layouts.admin', ['title' => $student->full_name . ' — Scores'])

@section('content')

<div class="max-w-4xl">
    <a href="{{ route('admin.students.show', $student) }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Student
    </a>

    {{-- Student header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $student->full_name }}
                </h2>
                <p class="text-xs font-mono text-gray-400 mt-0.5">
                    {{ $student->student_id }}
                </p>
            </div>
        </div>
    </div>

    {{-- Scores grouped by academic year --}}
    @forelse ($byYear as $yearName => $enrollments)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase
                       tracking-wide mb-3">
                {{ $yearName }}
            </h3>

            @foreach ($enrollments as $enrollment)
                <div class="bg-white rounded-lg shadow-sm border
                            border-gray-200 overflow-hidden mb-3">

                    {{-- Class label --}}
                    <div class="px-5 py-3 bg-gray-50 border-b border-gray-100
                                flex items-center justify-between">
                        <div>
                            <span class="text-sm font-semibold text-gray-700">
                                Class {{ $enrollment->schoolClass->name }}
                            </span>
                            <span class="text-xs text-gray-400 ml-2">
                                {{ $enrollment->schoolClass->grade->name }}
                            </span>
                        </div>
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $enrollment->status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </div>

                    @if ($enrollment->scores->isEmpty())
                        <div class="px-5 py-4 text-sm text-gray-400">
                            No scores recorded for this enrollment.
                        </div>
                    @else
                        {{-- Group scores by subject --}}
                        @php
                            $bySubject = $enrollment->scores
                                ->groupBy(fn($s) =>
                                    $s->examSession->subject->name
                                );
                        @endphp

                        @foreach ($bySubject as $subjectName => $scores)
                            <div class="px-5 py-3 border-b border-gray-100 last:border-0">
                                <p class="text-xs font-semibold text-gray-500
                                          uppercase tracking-wide mb-2">
                                    {{ $subjectName }}
                                </p>

                                <div class="space-y-1">
                                    @foreach ($scores as $score)
                                        <div class="flex items-center
                                                    justify-between text-sm">
                                            <div class="flex items-center gap-2">
                                                <span class="text-gray-700">
                                                    {{ $score->examSession->full_label }}
                                                </span>
                                                <span class="px-1.5 py-0.5 text-xs
                                                             rounded capitalize
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
                                                <span class="text-xs text-gray-400">
                                                    / {{ $score->examSession->max_score }}
                                                </span>
                                                <span class="text-xs text-gray-400 ml-1">
                                                    ({{ $score->percentage }}%)
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endforeach
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    px-5 py-8 text-center text-gray-400 text-sm">
            No score records found for this student.
        </div>
    @endforelse
</div>

@endsection