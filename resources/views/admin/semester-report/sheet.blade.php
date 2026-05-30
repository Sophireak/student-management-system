    @extends('layouts.admin', ['title' => 'Semester Report — ' . $class->name])

@section('content')

{{-- Header --}}
<div class="mb-4 flex items-center justify-between flex-wrap gap-2">
    <div>
        <h2 class="text-lg font-semibold text-gray-700">Semester Report</h2>
        <p class="text-sm text-gray-400 mt-0.5">
            {{ $class->name }} · {{ $class->grade->name }}
            · {{ $academicYear->name }}
            · {{ $semesterLabel }}
        </p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">

        {{-- Calculate button --}}
        @if (! $isLocked && $hasMonthlyData)
            <form method="POST"
                  action="{{ route('admin.semester-report.calculate') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="academic_year_id"
                       value="{{ $academicYearId }}">
                <input type="hidden" name="semester" value="{{ $semester }}">
                <button type="submit"
                        class="px-3 py-1.5 bg-green-600 text-white text-xs
                               font-medium rounded-md hover:bg-green-700"
                        onclick="return confirm('Calculate semester scores from monthly data? This will overwrite non-manual entries.')">
                    ⟳ Calculate from Monthly
                </button>
            </form>
        @endif

        {{-- Lock / Unlock --}}
        @if ($isLocked)
            <span class="px-3 py-1 text-xs font-semibold bg-red-100
                         text-red-700 rounded-full">🔒 Locked</span>
            <form method="POST"
                  action="{{ route('admin.semester-report.unlock') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="academic_year_id"
                       value="{{ $academicYearId }}">
                <input type="hidden" name="semester" value="{{ $semester }}">
                <button type="submit"
                        class="px-3 py-1 bg-yellow-100 text-yellow-700
                               text-xs rounded hover:bg-yellow-200">
                    Unlock
                </button>
            </form>
        @else
            <form method="POST"
                  action="{{ route('admin.semester-report.lock') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="academic_year_id"
                       value="{{ $academicYearId }}">
                <input type="hidden" name="semester" value="{{ $semester }}">
                <button type="submit"
                        class="px-3 py-1 bg-gray-100 text-gray-600
                               text-xs rounded hover:bg-gray-200">
                    🔓 Lock Report
                </button>
            </form>
        @endif
    </div>
</div>

{{-- Filters --}}
@include('partials.semester-report-filters', [
    'classes'          => $classes,
    'semesters'        => $semesters,
    'academicYears'    => $academicYears,
    'routePrefix'      => 'admin',
    'selectedYearId'   => $academicYearId,
    'selectedClassId'  => $class->id,
    'selectedSemester' => $semester,
])

@if (! $hasMonthlyData && $enrollments->isNotEmpty())
    <div class="mb-4 px-4 py-3 bg-yellow-50 border border-yellow-200
                text-yellow-800 rounded-md text-sm">
        ⚠️ No monthly score data found for this semester's months.
        Enter monthly scores first, then return here to calculate.
    </div>
@endif

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

    <form method="POST" action="{{ route('admin.semester-report.save') }}">
        @csrf
        <input type="hidden" name="class_id"          value="{{ $class->id }}">
        <input type="hidden" name="academic_year_id"  value="{{ $academicYearId }}">
        <input type="hidden" name="semester"          value="{{ $semester }}">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">

                    {{-- Info row --}}
                    <tr class="bg-blue-50">
                        <td colspan="{{ $subjects->count() + 5 }}"
                            class="px-4 py-2 text-xs text-blue-700">
                            <strong>{{ $class->grade->name }}</strong>
                            · Class {{ $class->name }}
                            · {{ $semesterLabel }}
                            · {{ $academicYear->name }}
                            @if ($isLocked)
                                · <span class="text-red-600 font-semibold">LOCKED</span>
                            @endif
                        </td>
                    </tr>

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
                                <div class="text-gray-400 font-normal normal-case text-xs">
                                    @if ($subject->isNumeric()) /{{ $subject->max_score }}
                                    @elseif ($subject->isGrade()) Grade
                                    @else P/F
                                    @endif
                                </div>
                            </th>
                        @endforeach
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-500 uppercase bg-blue-50 min-w-20">
                            Total
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-500 uppercase bg-blue-50 min-w-20">
                            Average
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-500 uppercase bg-yellow-50 min-w-16">
                            Rank
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($enrollments as $rowIndex => $enrollment)
                        @php
                            $rowSummary = $summary[$enrollment->id] ?? [];
                        @endphp
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
                                    $existing  = $matrix[$enrollment->id][$subject->id] ?? null;
                                    $inputKey  = "{$rowIndex}_{$colIndex}";
                                    $hasValue  = $existing !== null;
                                    $isOverride = $existing?->is_manual_override ?? false;
                                    $cellClass = $hasValue
                                        ? ($isOverride
                                            ? 'bg-yellow-50 border-yellow-300'
                                            : 'bg-green-50 border-green-200')
                                        : 'bg-white border-gray-200';
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
                                               min="0" max="{{ $subject->max_score }}"
                                               step="0.01" placeholder="—"
                                               {{ $isLocked ? 'readonly' : '' }}
                                               data-row="{{ $rowIndex }}"
                                               data-col="{{ $colIndex }}"
                                               class="score-input w-20 text-center border
                                                      rounded px-1 py-1 text-sm
                                                      focus:outline-none focus:ring-2
                                                      focus:ring-blue-400 {{ $cellClass }}
                                                      {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">

                                    @elseif ($subject->isGrade())
                                        <select name="scores[{{ $inputKey }}][grade]"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                data-row="{{ $rowIndex }}"
                                                data-col="{{ $colIndex }}"
                                                class="score-input w-36 border rounded
                                                       px-1 py-1 text-xs focus:outline-none
                                                       focus:ring-2 focus:ring-blue-400
                                                       {{ $cellClass }}">
                                            <option value="">—</option>
                                            @foreach (['Good','Satisfactory','Needs Improvement'] as $g)
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
                                                       {{ $cellClass }}">
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

                            {{-- Summary columns --}}
                            <td class="px-3 py-2 text-center bg-blue-50 font-semibold
                                       text-blue-800 text-sm">
                                {{ $rowSummary['total'] ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-center bg-blue-50 font-semibold
                                       text-blue-800 text-sm">
                                {{ $rowSummary['average'] ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-center bg-yellow-50 font-bold
                                       text-yellow-800 text-sm">
                                {{ $rowSummary['rank'] ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Legend --}}
        <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded bg-green-100 border border-green-200
                             inline-block"></span>
                Auto-calculated
            </span>
            <span class="flex items-center gap-1">
                <span class="w-3 h-3 rounded bg-yellow-100 border border-yellow-300
                             inline-block"></span>
                Manually overridden
            </span>
        </div>

        @if (! $isLocked)
            <div class="mt-4 flex items-center justify-between bg-white
                        rounded-lg shadow-sm border border-gray-200 px-5 py-4">
                <p class="text-xs text-gray-400">
                    Saving will recalculate totals, averages, and rankings.
                </p>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white text-sm
                               font-medium rounded-md hover:bg-blue-700">
                    Save Semester Report
                </button>
            </div>
        @else
            <div class="mt-4 px-5 py-3 bg-red-50 border border-red-200
                        rounded-lg text-sm text-red-700">
                🔒 This semester report is locked.
            </div>
        @endif
    </form>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs    = document.querySelectorAll(
        '.score-input:not([readonly]):not([disabled])'
    );
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
            const empty = this.value === '';
            this.classList.remove('bg-green-50', 'border-green-200',
                                  'bg-yellow-50', 'border-yellow-300',
                                  'bg-white', 'border-gray-200');
            if (!empty) {
                this.classList.add('bg-yellow-50', 'border-yellow-300');
            } else {
                this.classList.add('bg-white', 'border-gray-200');
            }
        });
    });
});
</script>
@endpush

@endsection