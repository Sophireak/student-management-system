@extends('layouts.admin', ['title' => $examSession->full_label])

@section('content')

<div class="max-w-3xl">
    <a href="{{ route('admin.exam-sessions.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Exam Sessions
    </a>

    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    {{ $examSession->full_label }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $examSession->schoolClass->name }}
                    · {{ $examSession->schoolClass->grade->name }}
                    · {{ $examSession->subject->name }}
                    · {{ $examSession->schoolClass->academicYear->name }}
                </p>
                <p class="text-sm text-gray-400 mt-0.5">
                    Max Score: {{ $examSession->max_score }}
                    @if ($examSession->exam_date)
                        · Date: {{ $examSession->exam_date->format('M d, Y') }}
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

        {{-- Score stats --}}
        @if ($examSession->scores->isNotEmpty())
            @php
                $scores = $examSession->scores->pluck('score');
                $avg    = round($scores->avg(), 1);
                $high   = $scores->max();
                $low    = $scores->min();
                $total  = $scores->count();
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 text-center text-sm">
                <div class="bg-blue-50 rounded-md p-3">
                    <p class="text-xl font-bold text-blue-700">{{ $total }}</p>
                    <p class="text-xs text-blue-600">Scored</p>
                </div>
                <div class="bg-green-50 rounded-md p-3">
                    <p class="text-xl font-bold text-green-700">{{ $avg }}</p>
                    <p class="text-xs text-green-600">Average</p>
                </div>
                <div class="bg-purple-50 rounded-md p-3">
                    <p class="text-xl font-bold text-purple-700">{{ $high }}</p>
                    <p class="text-xs text-purple-600">Highest</p>
                </div>
                <div class="bg-yellow-50 rounded-md p-3">
                    <p class="text-xl font-bold text-yellow-700">{{ $low }}</p>
                    <p class="text-xs text-yellow-600">Lowest</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Score list --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
            <h3 class="text-sm font-semibold text-gray-700">Student Scores</h3>
        </div>

        @forelse ($examSession->scores as $score)
            <div class="flex items-center justify-between px-5 py-3
                        border-b border-gray-100 last:border-0 text-sm">
                <div>
                    <p class="font-medium text-gray-800">
                        {{ $score->enrollment->student->full_name }}
                    </p>
                    @if ($score->remarks)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $score->remarks }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-800">
                        {{ $score->score }}
                        <span class="text-xs font-normal text-gray-400">
                            / {{ $examSession->max_score }}
                        </span>
                    </p>
                    <p class="text-xs text-gray-400">{{ $score->percentage }}%</p>
                </div>
            </div>
        @empty
            <div class="px-5 py-6 text-center text-gray-400 text-sm">
                No scores entered yet.
            </div>
        @endforelse
    </div>
</div>

@endsection