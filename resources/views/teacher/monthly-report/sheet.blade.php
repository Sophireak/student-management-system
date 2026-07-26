@extends('layouts.teacher', ['title' => 'Monthly Report — ' . $class->name])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <div>
        <h2 class="text-lg font-semibold text-gray-700">Monthly Score Report</h2>
        <p class="text-sm text-gray-400 mt-0.5">
            {{ $class->name }}
            · {{ $class->grade->name }}
            · Month {{ $month }} — {{ $monthName }}
        </p>
    </div>
    @if ($isLocked)
        <span class="px-3 py-1 text-xs font-semibold bg-red-100
                     text-red-700 rounded-full">
            🔒 Locked by Admin
        </span>
    @endif
</div>

@php
    $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
@endphp

@include('partials.monthly-report-filters', [
    'classes'         => $classes,
    'months'          => $months,
    'academicYears'   => collect(),
    'routePrefix'     => 'teacher',
    'selectedYearId'  => $activeYear?->id,
    'selectedClassId' => $class->id,
    'selectedMonth'   => $month,
])

@if ($enrollments->isEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200
                px-5 py-8 text-center text-gray-400 text-sm">
        No active students in this class.
    </div>
@elseif ($subjects->isEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200
                px-5 py-8 text-center text-gray-400 text-sm">
        No subjects configured for {{ $class->grade->name }}.
    </div>
@else

    <form method="POST"
          action="{{ route('teacher.monthly-report.save') }}">
        @csrf

        <input type="hidden" name="class_id"
               value="{{ $class->id }}">
        <input type="hidden" name="academic_year_id"
               value="{{ $academicYearId }}">
        <input type="hidden" name="month" value="{{ $month }}">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-500 uppercase border-r border-gray-200
                                   w-8 sticky left-0 bg-gray-50 z-10">
                            No.
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold
                                   text-gray-500 uppercase border-r border-gray-200
                                   w-48 sticky left-8 bg-gray-50 z-10">
                            Student Name
                        </th>
                        @foreach ($subjects as $subject)
                            <th class="px-3 py-3 text-center text-xs font-semibold
                                       text-gray-500 uppercase border-r border-gray-100
                                       min-w-24">
                                <div>{{ $subject->name }}</div>
                                <div class="text-gray-400 font-normal
                                            normal-case text-xs mt-0.5">
                                    @if ($subject->isNumeric())
                                        /{{ $subject->max_score }}
                                    @elseif ($subject->isGrade())
                                        Grade
                                    @else
                                        P/F
                                    @endif
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($enrollments as $rowIndex => $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-center text-xs text-gray-400
                                       border-r border-gray-200
                                       sticky left-0 bg-white z-10">
                                {{ $rowIndex + 1 }}
                            </td>
                            <td class="px-4 py-2 font-medium text-gray-800
                                       border-r border-gray-200 whitespace-nowrap
                                       sticky left-8 bg-white z-10">
                                {{ $enrollment->student->full_name }}
                            </td>

                            @foreach ($subjects as $colIndex => $subject)
                                @php
                                    $existing = $matrix[$enrollment->id][$subject->id] ?? null;
                                    $inputKey = "{$rowIndex}_{$colIndex}";
                                    $hasValue = $existing !== null;
                                @endphp
                                <td class="px-1 py-1 text-center border-r border-gray-100">

                                    <input type="hidden"
                                           name="scores[{{ $inputKey }}][enrollment_id]"
                                           value="{{ $enrollment->id }}">
                                    <input type="hidden"
                                           name="scores[{{ $inputKey }}][subject_id]"
                                           value="{{ $subject->id }}">

                                    @if ($subject->isNumeric())
                                        <input type="number"
                                               name="scores[{{ $inputKey }}][score]"
                                               value="{{ $existing?->score }}"
                                               min="0"
                                               max="{{ $subject->max_score }}"
                                               step="0.5"
                                               placeholder="—"
                                               {{ $isLocked ? 'readonly' : '' }}
                                               data-row="{{ $rowIndex }}"
                                               data-col="{{ $colIndex }}"
                                               class="score-input w-20 text-center
                                                      border rounded px-1 py-1 text-sm
                                                      focus:outline-none focus:ring-2
                                                      focus:ring-blue-400
                                                      {{ $hasValue
                                                          ? 'bg-green-50 border-green-200'
                                                          : 'bg-white border-gray-200' }}
                                                      {{ $isLocked
                                                          ? 'opacity-60 cursor-not-allowed'
                                                          : '' }}">

                                    @elseif ($subject->isGrade())
                                        <select name="scores[{{ $inputKey }}][grade]"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                data-row="{{ $rowIndex }}"
                                                data-col="{{ $colIndex }}"
                                                class="score-input w-36 border rounded
                                                       px-1 py-1 text-xs focus:outline-none
                                                       focus:ring-2 focus:ring-blue-400
                                                       {{ $hasValue
                                                           ? 'bg-green-50 border-green-200'
                                                           : 'bg-white border-gray-200' }}">
                                            <option value="">—</option>
                                            @foreach (['Good', 'Satisfactory', 'Needs Improvement'] as $g)
                                                <option value="{{ $g }}"
                                                    {{ $existing?->grade === $g ? 'selected' : '' }}>
                                                    {{ $g }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @else
                                        <select name="scores[{{ $inputKey }}][pass_fail]"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                data-row="{{ $rowIndex }}"
                                                data-col="{{ $colIndex }}"
                                                class="score-input w-20 border rounded
                                                       px-1 py-1 text-xs focus:outline-none
                                                       focus:ring-2 focus:ring-blue-400
                                                       {{ $hasValue
                                                           ? 'bg-green-50 border-green-200'
                                                           : 'bg-white border-gray-200' }}">
                                            <option value="">—</option>
                                            <option value="Pass"
                                                {{ $existing?->pass_fail === 'Pass' ? 'selected' : '' }}>
                                                Pass
                                            </option>
                                            <option value="Fail"
                                                {{ $existing?->pass_fail === 'Fail' ? 'selected' : '' }}>
                                                Fail
                                            </option>
                                        </select>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (! $isLocked)
            <div class="mt-4 flex items-center justify-between bg-white
                        rounded-lg shadow-sm border border-gray-200 px-5 py-4">
                <p class="text-xs text-gray-400">
                    Green cells are already saved.
                    Empty cells will be skipped.
                </p>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white text-sm
                               font-medium rounded-md hover:bg-blue-700">
                    Save Monthly Report
                </button>
            </div>
        @else
            <div class="mt-4 px-5 py-3 bg-red-50 border border-red-200
                        rounded-lg text-sm text-red-700">
                🔒 This report has been locked by the administrator.
            </div>
        @endif

    </form>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs    = document.querySelectorAll('.score-input:not([readonly]):not([disabled])');
    const totalCols = {{ $subjects->count() }};
    const totalRows = {{ $enrollments->count() }};

    inputs.forEach(input => {
        if (input.tagName === 'INPUT') {
            input.addEventListener('keydown', function (e) {
                const row = parseInt(this.dataset.row);
                const col = parseInt(this.dataset.col);
                let nextRow = row, nextCol = col;

                if (e.key === 'ArrowRight' || (e.key === 'Tab' && !e.shiftKey)) {
                    e.preventDefault();
                    nextCol = col + 1 < totalCols ? col + 1 : 0;
                    if (nextCol === 0) nextRow = row + 1 < totalRows ? row + 1 : 0;
                } else if (e.key === 'ArrowLeft' || (e.key === 'Tab' && e.shiftKey)) {
                    e.preventDefault();
                    nextCol = col - 1 >= 0 ? col - 1 : totalCols - 1;
                    if (nextCol === totalCols - 1)
                        nextRow = row - 1 >= 0 ? row - 1 : totalRows - 1;
                } else if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    e.preventDefault();
                    nextRow = row + 1 < totalRows ? row + 1 : 0;
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    nextRow = row - 1 >= 0 ? row - 1 : totalRows - 1;
                } else { return; }

                const next = document.querySelector(
                    `.score-input[data-row="${nextRow}"][data-col="${nextCol}"]`
                );
                if (next) { next.focus(); if (next.select) next.select(); }
            });
        }

        input.addEventListener('change', function () {
            const empty = this.value === '' || this.value === null;
            this.classList.toggle('bg-green-50', !empty);
            this.classList.toggle('border-green-200', !empty);
            this.classList.toggle('bg-white', empty);
            this.classList.toggle('border-gray-200', empty);
        });
    });
});
</script>
@endpush

@endsection