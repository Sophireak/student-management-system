@extends('layouts.teacher', ['title' => 'Report — ' . $class->name])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $backUrl     = route($routePrefix . '.scores.index', [
        'class_id' => $class->id,
        'period'   => $selectedPeriod,
    ]);

    // Class statistics
    $totalStudents = $enrollments->count();
    $averages      = collect($summary)->pluck('average')->filter();
    $classAverage  = $averages->count() > 0 ? round($averages->avg(), 2) : null;
    $passCount     = $averages->filter(fn ($a) => $a >= 5.00)->count();
    $passRate      = $totalStudents > 0 ? round(($passCount / $totalStudents) * 100) : 0;
@endphp

{{-- Print styles --}}
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white !important; }
        .print-page { padding: 0; margin: 0; }
        table { font-size: 10px !important; }
        .print-title { display: block !important; }
    }
    .print-title { display: none; }

    /* Vertical text for subject headers */
    .rotate-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        white-space: nowrap;
    }
</style>

{{-- Back + Actions (hidden when printing) --}}
<div class="no-print">
    <a href="{{ $backUrl }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Score Dashboard
    </a>

    <div class="flex flex-wrap items-start justify-between gap-3 mb-5">
    <div>
        <p class="text-sm text-gray-500">
            {{ $class->name }} · {{ $class->grade->name }} · {{ $periodLabel }} · {{ $class->academicYear->name }}
        </p>
    </div>

        <div class="flex flex-wrap items-center gap-2">
            <button onclick="window.print()"
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <i class="ti ti-printer text-base"></i> Print
            </button>

            <button type="button" disabled
                    class="flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed"
                    title="Coming soon">
                <i class="ti ti-file-type-pdf text-base"></i> PDF
            </button>

            <button type="button" disabled
                    class="flex items-center gap-2 px-4 py-2 bg-gray-50 border border-gray-200 text-gray-400 text-sm font-medium rounded-lg cursor-not-allowed"
                    title="Coming soon">
                <i class="ti ti-file-spreadsheet text-base"></i> Excel
            </button>
        </div>
    </div>

    {{-- Class Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Students</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalStudents }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $subjects->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Class Avg</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">
                {{ $classAverage !== null ? $classAverage : '—' }}
                <span class="text-sm text-gray-400 font-normal">/10</span>
            </p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Pass Rate</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $passRate }}%</p>
        </div>
    </div>
</div>

{{-- Print-only Header --}}
<div class="print-title mb-4 text-center">
    <h1 class="text-xl font-bold">{{ $class->name }} — {{ $class->grade->name }}</h1>
    <p class="text-sm">{{ $periodLabel }} · {{ $class->academicYear->name }}</p>
</div>

{{-- Report Table --}}
@if ($enrollments->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center no-print">
        <i class="ti ti-users-off text-5xl text-gray-300 block mb-3"></i>
        <p class="text-gray-500 text-sm">No active students in this class.</p>
    </div>

@elseif ($subjects->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center no-print">
        <i class="ti ti-book-off text-5xl text-gray-300 block mb-3"></i>
        <p class="text-gray-500 text-sm">No subjects configured for this grade.</p>
    </div>

@else
    <div class="bg-white rounded-xl border border-gray-200 overflow-x-auto print-page">
        <table class="min-w-full text-sm border-collapse">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 w-10">
                        No.
                    </th>
                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 min-w-40">
                        Student Name
                    </th>
                    <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 w-14">
    Gender
</th>

                    {{-- Subject headers (rotated for space) --}}
                    @foreach ($subjects as $subject)
                        <th class="px-1 py-3 text-center border-r border-gray-100"
                            style="height: 120px; min-width: 40px; max-width: 40px;">
                            <div class="rotate-text text-xs font-semibold text-gray-700 mx-auto">
                                {{ $subject->name }}
                            </div>
                        </th>
                    @endforeach

                    <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 w-16 bg-gray-100">
                        Total
                    </th>
                    <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 w-16 bg-gray-100">
                        Avg
                    </th>
                    <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase border-r border-gray-200 w-14 bg-gray-100">
                        Rank
                    </th>
                    <th class="px-2 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-20 bg-gray-100">
                        Grade
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100">
                @foreach ($enrollments as $index => $enrollment)
                    @php $sum = $summary[$enrollment->id] ?? []; @endphp

                    <tr class="hover:bg-gray-50">
                        {{-- No --}}
                        <td class="px-2 py-2 text-center text-xs text-gray-500 border-r border-gray-200">
                            {{ $index + 1 }}
                        </td>

                        {{-- Name --}}
                        <td class="px-3 py-2 text-sm font-medium text-gray-800 border-r border-gray-200 whitespace-nowrap">
                            {{ $enrollment->student->full_name }}
                        </td>

                        {{-- Gender --}}
                        <td class="px-2 py-2 text-center border-r border-gray-200">
    @php $g = strtolower($enrollment->student->gender ?? ''); @endphp
    @if ($g === 'male')
        <span class="text-blue-600 text-xs font-medium">ប្រុស</span>
    @elseif ($g === 'female')
        <span class="text-pink-600 text-xs font-medium">ស្រី</span>
    @else
        <span class="text-gray-300 text-xs">—</span>
    @endif
</td>

                        {{-- Subject scores --}}
                        @foreach ($subjects as $subject)
                            @php $score = $matrix[$enrollment->id][$subject->id] ?? null; @endphp
                            <td class="px-1 py-2 text-center text-xs border-r border-gray-100">
                                @if ($score)
                                    @if ($subject->isNumeric() && $score->score !== null)
                                        @php $g = \App\Helpers\ScoreHelper::grade((float) $score->score); @endphp
                                        <span class="text-{{ $g['color'] }}-700 font-medium">
                                            {{ number_format($score->score, 2) }}
                                        </span>
                                    @elseif ($subject->isGrade() && $score->grade)
                                        <span class="text-gray-700 text-[10px]">{{ $score->grade }}</span>
                                    @elseif ($subject->isPassFail() && $score->pass_fail)
                                        <span class="text-{{ $score->pass_fail === 'Pass' ? 'green' : 'red' }}-600 font-medium">
                                            {{ $score->pass_fail === 'Pass' ? 'P' : 'F' }}
                                        </span>
                                    @else
                                        <span class="text-gray-300">—</span>
                                    @endif
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>
                        @endforeach

                        {{-- Total --}}
                        <td class="px-2 py-2 text-center text-sm text-gray-700 font-medium border-r border-gray-200 bg-gray-50">
                            {{ $sum['total'] ?? '—' }}
                        </td>

                        {{-- Average --}}
                        <td class="px-2 py-2 text-center text-sm font-semibold border-r border-gray-200 bg-gray-50">
                            @if (isset($sum['average']))
                                @php $g = \App\Helpers\ScoreHelper::grade($sum['average']); @endphp
                                <span class="text-{{ $g['color'] }}-700">
                                    {{ number_format($sum['average'], 2) }}
                                </span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>

                        {{-- Rank --}}
                        <td class="px-2 py-2 text-center text-sm font-bold text-gray-700 border-r border-gray-200 bg-gray-50">
                            {{ $sum['rank'] ?? '—' }}
                        </td>

                        {{-- Grade Label --}}
                        <td class="px-2 py-2 text-center bg-gray-50">
                            @if (isset($sum['average']))
                                @php $g = \App\Helpers\ScoreHelper::grade($sum['average']); @endphp
                                <span class="inline-block px-2 py-0.5 text-[10px] font-medium rounded
                                             bg-{{ $g['color'] }}-50 text-{{ $g['color'] }}-700">
                                    {{ $g['kh'] }}
                                </span>
                            @else
                                <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Grade Legend --}}
    <div class="mt-5 bg-white rounded-xl border border-gray-200 p-4 no-print">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Grade Scale</p>
        <div class="flex flex-wrap gap-2">
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-green-50 text-green-700 rounded-md">
                9.00–10.00 · ល្អណាស់
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-blue-50 text-blue-700 rounded-md">
                8.00–8.99 · ល្អ
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-orange-50 text-orange-700 rounded-md">
                7.00–7.99 · ល្អបង្គួរ
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-yellow-50 text-yellow-700 rounded-md">
                6.00–6.99 · មធ្យម
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-amber-50 text-amber-700 rounded-md">
                5.00–5.99 · ខ្សោយ
            </span>
            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs bg-red-50 text-red-700 rounded-md">
                0.00–4.99 · ធ្លាក់
            </span>
        </div>
    </div>
@endif

@endsection