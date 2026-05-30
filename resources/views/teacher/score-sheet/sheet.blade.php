@extends('layouts.admin', ['title' => 'Score Sheet — ' . $class->name])

@section('content')

<div class="mb-4 flex items-center justify-between flex-wrap gap-2">
    <div>
        <h2 class="text-lg font-semibold text-gray-700">Score Sheet</h2>
        <p class="text-sm text-gray-400 mt-0.5">
            {{ $class->name }}
            · {{ $class->grade->name }}
            · {{ $examSession->full_label }}
            · Max: {{ $maxScore }}
        </p>
    </div>
    <a href="{{ route('teacher.score-sheet.index', ['class_id' => $class->id]) }}"
       class="text-sm text-gray-500 hover:text-gray-700">
        ← Change Selection
    </a>
</div>

@include('partials.score-sheet-filters', [
    'classes'         => $classes,
    'examSessions'    => $examSessions,
    'selectedClassId' => $selectedClassId,
    'routePrefix'     => 'teacher',
])

@if ($enrollments->isEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200
                px-5 py-8 text-center text-gray-400 text-sm">
        No active students enrolled in this class.
    </div>
@elseif ($subjects->isEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200
                px-5 py-8 text-center text-gray-400 text-sm">
        No subjects found for {{ $class->grade->name }}.
    </div>
@else
    <form method="POST"
          action="{{ route('teacher.score-sheet.save') }}"
          id="scoreSheetForm">
        @csrf

        <input type="hidden"
               name="exam_session_id"
               value="{{ $examSession->id }}">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wide
                                   border-r border-gray-200 w-48 sticky
                                   left-0 bg-gray-50 z-10">
                            Student
                        </th>
                        @foreach ($subjects as $subject)
                            <th class="px-3 py-3 text-center text-xs
                                       font-semibold text-gray-500 uppercase
                                       tracking-wide border-r border-gray-100
                                       min-w-24">
                                {{ $subject->name }}
                            </th>
                        @endforeach
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-500 uppercase tracking-wide
                                   bg-blue-50 min-w-20">
                            Avg
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($enrollments as $rowIndex => $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-800
                                       border-r border-gray-200 whitespace-nowrap
                                       sticky left-0 bg-white z-10">
                                {{ $enrollment->student->full_name }}
                            </td>

                            @foreach ($subjects as $colIndex => $subject)
                                @php
                                    $existing = $matrix[$enrollment->id][$subject->id] ?? null;
                                @endphp
                                <td class="px-1 py-1 text-center
                                           border-r border-gray-100">
                                    <input type="hidden"
                                           name="scores[{{ $rowIndex }}_{{ $colIndex }}][enrollment_id]"
                                           value="{{ $enrollment->id }}">
                                    <input type="hidden"
                                           name="scores[{{ $rowIndex }}_{{ $colIndex }}][subject_id]"
                                           value="{{ $subject->id }}">
                                    <input type="number"
                                           name="scores[{{ $rowIndex }}_{{ $colIndex }}][score]"
                                           value="{{ $existing }}"
                                           min="0"
                                           max="{{ $maxScore }}"
                                           step="0.5"
                                           placeholder="—"
                                           data-row="{{ $rowIndex }}"
                                           data-col="{{ $colIndex }}"
                                           data-max="{{ $maxScore }}"
                                           class="score-input w-20 text-center
                                                  border border-gray-200 rounded
                                                  px-1 py-1 text-sm
                                                  focus:outline-none
                                                  focus:ring-2 focus:ring-blue-400
                                                  {{ $existing !== null
                                                      ? 'bg-green-50 border-green-200'
                                                      : 'bg-white' }}">
                                </td>
                            @endforeach

                            <td class="px-3 py-2 text-center bg-blue-50
                                       text-sm font-semibold text-blue-700"
                                id="avg-row-{{ $rowIndex }}">
                                —
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot class="bg-gray-50 border-t-2 border-gray-200">
                    <tr>
                        <td class="px-4 py-3 text-xs font-semibold text-gray-500
                                   uppercase border-r border-gray-200
                                   sticky left-0 bg-gray-50 z-10">
                            Column Avg
                        </td>
                        @foreach ($subjects as $colIndex => $subject)
                            <td class="px-3 py-3 text-center text-sm font-bold
                                       text-blue-700 border-r border-gray-100"
                                id="avg-col-{{ $colIndex }}">
                                —
                            </td>
                        @endforeach
                        <td class="px-3 py-3 text-center bg-blue-50"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between bg-white
                    rounded-lg shadow-sm border border-gray-200 px-5 py-4">
            <div class="text-sm text-gray-500">
                <span id="filledCount">0</span> of
                <span>{{ $enrollments->count() * $subjects->count() }}</span>
                cells filled
            </div>
            <div class="flex items-center gap-3">
                <button type="button"
                        onclick="fillAllZero()"
                        class="px-4 py-2 bg-gray-100 text-gray-600 text-sm
                               font-medium rounded-md hover:bg-gray-200">
                    Fill Empty with 0
                </button>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white text-sm
                               font-medium rounded-md hover:bg-blue-700">
                    Save All Scores
                </button>
            </div>
        </div>
    </form>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs    = document.querySelectorAll('.score-input');
    const totalCols = {{ $subjects->count() }};
    const totalRows = {{ $enrollments->count() }};
    const maxScore  = {{ $maxScore }};

    inputs.forEach(input => {
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
                if (nextCol === totalCols - 1) nextRow = row - 1 >= 0 ? row - 1 : totalRows - 1;
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
            if (next) { next.focus(); next.select(); }
        });

        input.addEventListener('input', function () {
            const val = parseFloat(this.value);
            if (this.value === '') {
                this.classList.remove('bg-green-50','border-green-200',
                                      'bg-red-50','border-red-400');
                this.classList.add('bg-white');
            } else if (isNaN(val) || val < 0 || val > maxScore) {
                this.classList.remove('bg-green-50','border-green-200','bg-white');
                this.classList.add('bg-red-50','border-red-400');
            } else {
                this.classList.remove('bg-red-50','border-red-400','bg-white');
                this.classList.add('bg-green-50','border-green-200');
            }
            recalculate();
        });
    });

    function recalculate() {
        let filled = 0;
        for (let r = 0; r < totalRows; r++) {
            const rowInputs = document.querySelectorAll(`.score-input[data-row="${r}"]`);
            let sum = 0, count = 0;
            rowInputs.forEach(inp => {
                const v = parseFloat(inp.value);
                if (!isNaN(v) && inp.value !== '') { sum += v; count++; filled++; }
            });
            const el = document.getElementById(`avg-row-${r}`);
            if (el) el.textContent = count > 0 ? (sum/count).toFixed(1) : '—';
        }
        for (let c = 0; c < totalCols; c++) {
            const colInputs = document.querySelectorAll(`.score-input[data-col="${c}"]`);
            let sum = 0, count = 0;
            colInputs.forEach(inp => {
                const v = parseFloat(inp.value);
                if (!isNaN(v) && inp.value !== '') { sum += v; count++; }
            });
            const el = document.getElementById(`avg-col-${c}`);
            if (el) el.textContent = count > 0 ? (sum/count).toFixed(1) : '—';
        }
        const counter = document.getElementById('filledCount');
        if (counter) counter.textContent = filled;
    }

    window.fillAllZero = function () {
        inputs.forEach(inp => {
            if (inp.value === '') {
                inp.value = 0;
                inp.classList.remove('bg-red-50','border-red-400','bg-white');
                inp.classList.add('bg-green-50','border-green-200');
            }
        });
        recalculate();
    };

    recalculate();
});
</script>
@endpush

@endsection