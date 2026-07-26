@extends('layouts.teacher', ['title' => $examSession->full_label])

@section('content')

<div class="max-w-3xl">
    <a href="{{ route('teacher.exam-sessions.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Exam Sessions
    </a>

    {{-- Session info --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-800">
                    {{ $examSession->full_label }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $examSession->schoolClass->name }}
                    · {{ $examSession->schoolClass->grade->name }}
                    · {{ $examSession->subject->name }}
                </p>
                <p class="text-sm text-gray-400 mt-0.5">
                    Max Score: {{ $examSession->max_score }}
                    @if ($examSession->exam_date)
                        · {{ $examSession->exam_date->format('M d, Y') }}
                    @endif
                </p>
            </div>
            <span class="px-3 py-1 text-xs font-semibold rounded-full capitalize
                {{ match($examSession->type) {
                    'quiz'     => 'bg-purple-100 text-purple-700',
                    'monthly'  => 'bg-blue-100 text-blue-700',
                    'semester' => 'bg-yellow-100 text-yellow-700',
                    'final'    => 'bg-red-100 text-red-700',
                } }}">
                {{ ucfirst($examSession->type) }}
            </span>
        </div>
    </div>

    {{-- Enter scores for unscored students --}}
    @if ($unscoredEnrollments->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-hidden mb-4">
            <div class="px-5 py-3 bg-yellow-50 border-b border-yellow-100">
                <h3 class="text-sm font-semibold text-yellow-800">
                    Enter Scores
                    <span class="font-normal text-yellow-600">
                        ({{ $unscoredEnrollments->count() }} students pending)
                    </span>
                </h3>
            </div>

            <form method="POST"
                  action="{{ route('teacher.exam-sessions.scores.store', $examSession) }}"
                  novalidate>
                @csrf

                @foreach ($unscoredEnrollments as $index => $enrollment)
                    <div class="px-5 py-3 border-b border-gray-100 last:border-0">
                        <input type="hidden"
                               name="scores[{{ $index }}][enrollment_id]"
                               value="{{ $enrollment->id }}">

                        <div class="flex items-center gap-4">
                            <p class="text-sm font-medium text-gray-800 w-44 flex-shrink-0">
                                {{ $enrollment->student->full_name }}
                            </p>

                            <div class="flex items-center gap-2 flex-1">
                                <div class="relative">
                                    <input type="number"
                                           name="scores[{{ $index }}][score]"
                                           value="{{ old("scores.{$index}.score", 0) }}"
                                           min="0"
                                           max="{{ $examSession->max_score }}"
                                           step="0.01"
                                           class="w-24 border border-gray-300 rounded-md
                                                  px-3 py-1.5 text-sm text-center
                                                  focus:outline-none focus:ring-2
                                                  focus:ring-blue-500">
                                    <span class="absolute right-2 top-1.5 text-xs
                                                 text-gray-400 pointer-events-none">
                                        / {{ $examSession->max_score }}
                                    </span>
                                </div>

                                <input type="text"
                                       name="scores[{{ $index }}][remarks]"
                                       placeholder="Remarks (optional)"
                                       class="flex-1 border border-gray-200 rounded-md
                                              px-3 py-1.5 text-xs text-gray-600
                                              focus:outline-none focus:ring-1
                                              focus:ring-blue-400">
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white text-sm font-medium
                                   rounded-md hover:bg-blue-700">
                        Save Scores
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Already scored students --}}
    @if ($examSession->scores->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">
                    Entered Scores
                    @if ($isFullyScored)
                        <span class="ml-2 px-2 py-0.5 text-xs bg-green-100
                                     text-green-700 rounded-full">
                            Complete
                        </span>
                    @endif
                </h3>
            </div>

            @foreach ($examSession->scores as $score)
                <div class="flex items-center justify-between px-5 py-3
                            border-b border-gray-100 last:border-0">
                    <p class="text-sm font-medium text-gray-800 w-44 flex-shrink-0">
                        {{ $score->enrollment->student->full_name }}
                    </p>

                    {{-- Inline update --}}
                    <form method="POST"
                          action="{{ route('teacher.exam-sessions.scores.update',
                                          [$examSession, $score]) }}"
                          class="flex items-center gap-2 flex-1">
                        @csrf
                        @method('PATCH')

                        <div class="relative">
                            <input type="number"
                                   name="score"
                                   value="{{ $score->score }}"
                                   min="0"
                                   max="{{ $examSession->max_score }}"
                                   step="0.01"
                                   class="w-24 border border-gray-200 rounded-md
                                          px-3 py-1 text-sm text-center
                                          focus:outline-none focus:ring-1
                                          focus:ring-blue-400">
                            <span class="absolute right-2 top-1 text-xs
                                         text-gray-400 pointer-events-none">
                                / {{ $examSession->max_score }}
                            </span>
                        </div>

                        <input type="text"
                               name="remarks"
                               value="{{ $score->remarks }}"
                               placeholder="Remarks"
                               class="flex-1 border border-gray-200 rounded-md
                                      px-3 py-1 text-xs text-gray-600
                                      focus:outline-none focus:ring-1
                                      focus:ring-blue-400">

                        <span class="text-xs text-gray-400 w-10 text-right">
                            {{ $score->percentage }}%
                        </span>

                        <button type="submit"
                                class="text-xs px-2 py-1 bg-gray-100 text-gray-600
                                       rounded hover:bg-gray-200">
                            Update
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection