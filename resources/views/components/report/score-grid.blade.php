<div class="report-sheet-wrapper">

    {{-- Status bar --}}
    <div class="flex items-center justify-between mb-2 text-xs text-gray-500">
        <div class="flex items-center gap-3">
            <span>
                <span class="inline-block w-3 h-3 rounded bg-green-100
                             border border-green-300 mr-1"></span>
                Saved
            </span>
            <span>
                <span class="inline-block w-3 h-3 rounded bg-blue-100
                             border border-blue-300 mr-1"></span>
                Modified
            </span>
            <span>
                <span class="inline-block w-3 h-3 rounded bg-red-100
                             border border-red-300 mr-1"></span>
                Invalid
            </span>
        </div>
        <div id="cellStatus" class="text-gray-400">
            Ready
        </div>
    </div>

    <form method="POST"
          action="{{ $saveRoute }}"
          id="reportForm"
          novalidate>
        @csrf

        {{-- Pass extra hidden fields (class_id, month, semester, etc) --}}
        @foreach ($hiddenFields as $name => $value)
            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
        @endforeach

        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-x-auto" id="sheetContainer">
            <table class="report-table min-w-full text-sm border-collapse"
                   id="reportTable">

                {{-- ── Column Headers ── --}}
                <thead class="bg-gray-50 border-b-2 border-gray-300">
                    <tr>

                        {{-- Row number --}}
                        <th class="report-th w-10 sticky left-0 bg-gray-50 z-20
                                   border-r border-gray-300">
                            #
                        </th>

                        {{-- Student name --}}
                        <th class="report-th text-left w-52 sticky left-10
                                   bg-gray-50 z-20 border-r-2 border-gray-300">
                            Student Name
                        </th>

                        {{-- Subject columns --}}
                        @foreach ($subjects as $colIndex => $subject)
                            <th class="report-th min-w-24 border-r border-gray-200"
                                data-col="{{ $colIndex }}">
                                <div class="font-semibold text-gray-700">
                                    {{ $subject->name }}
                                </div>
                                <div class="text-gray-400 font-normal mt-0.5">
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

                        {{-- Optional: row average --}}
                        @if ($showRowAverage)
                            <th class="report-th min-w-20 bg-blue-50
                                       border-l-2 border-blue-200">
                                Avg
                            </th>
                        @endif

                        {{-- Optional: rank --}}
                        @if ($showRank)
                            <th class="report-th min-w-16 bg-yellow-50
                                       border-l border-yellow-200">
                                Rank
                            </th>
                        @endif

                    </tr>
                </thead>

                {{-- ── Data Rows ── --}}
                <tbody id="reportBody">
                    @foreach ($enrollments as $rowIndex => $enrollment)
                        <tr class="report-row hover:bg-gray-50 transition-colors"
                            data-row="{{ $rowIndex }}"
                            id="row-{{ $rowIndex }}">

                            {{-- Row number --}}
                            <td class="report-td text-center text-gray-400
                                       sticky left-0 bg-white z-10
                                       border-r border-gray-200">
                                {{ $rowIndex + 1 }}
                            </td>

                            {{-- Student name --}}
                            <td class="report-td font-medium text-gray-800
                                       sticky left-10 bg-white z-10
                                       border-r-2 border-gray-300 whitespace-nowrap">
                                <div>{{ $enrollment->student->full_name }}</div>
                                <div class="text-xs text-gray-400 font-normal">
                                    {{ $enrollment->student->student_id }}
                                </div>
                            </td>

                            {{-- Score cells --}}
                            @foreach ($subjects as $colIndex => $subject)
                                @php
                                    $cell     = $matrix[$enrollment->id][$subject->id] ?? null;
                                    $inputKey = "{$rowIndex}_{$colIndex}";
                                    $hasValue = $cell !== null;
                                @endphp
                                <td class="report-cell p-0 border-r border-gray-100
                                           {{ $hasValue ? 'has-value' : '' }}"
                                    data-row="{{ $rowIndex }}"
                                    data-col="{{ $colIndex }}">

                                    <input type="hidden"
                                           name="scores[{{ $inputKey }}][enrollment_id]"
                                           value="{{ $enrollment->id }}">
                                    <input type="hidden"
                                           name="scores[{{ $inputKey }}][subject_id]"
                                           value="{{ $subject->id }}">

                                    @if ($subject->isNumeric())
                                        <input type="number"
                                               name="scores[{{ $inputKey }}][score]"
                                               value="{{ $hasValue ? $cell->score ?? $cell : '' }}"
                                               min="0"
                                               max="{{ $subject->max_score }}"
                                               step="0.5"
                                               placeholder="—"
                                               autocomplete="off"
                                               {{ $isLocked ? 'readonly' : '' }}
                                               data-row="{{ $rowIndex }}"
                                               data-col="{{ $colIndex }}"
                                               data-type="numeric"
                                               data-max="{{ $subject->max_score }}"
                                               data-original="{{ $hasValue ? ($cell->score ?? $cell) : '' }}"
                                               class="cell-input
                                                      {{ $hasValue ? 'cell-saved' : 'cell-empty' }}
                                                      {{ $isLocked ? 'cell-locked' : '' }}">

                                    @elseif ($subject->isGrade())
                                        <select name="scores[{{ $inputKey }}][grade]"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                data-row="{{ $rowIndex }}"
                                                data-col="{{ $colIndex }}"
                                                data-type="grade"
                                                data-original="{{ $hasValue ? ($cell->grade ?? '') : '' }}"
                                                class="cell-input cell-select
                                                       {{ $hasValue ? 'cell-saved' : 'cell-empty' }}
                                                       {{ $isLocked ? 'cell-locked' : '' }}">
                                            <option value="">—</option>
                                            @foreach (['Good', 'Satisfactory', 'Needs Improvement'] as $g)
                                                <option value="{{ $g }}"
                                                    {{ ($cell->grade ?? '') === $g ? 'selected' : '' }}>
                                                    {{ $g }}
                                                </option>
                                            @endforeach
                                        </select>

                                    @else
                                        <select name="scores[{{ $inputKey }}][pass_fail]"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                data-row="{{ $rowIndex }}"
                                                data-col="{{ $colIndex }}"
                                                data-type="pass_fail"
                                                data-original="{{ $hasValue ? ($cell->pass_fail ?? '') : '' }}"
                                                class="cell-input cell-select
                                                       {{ $hasValue ? 'cell-saved' : 'cell-empty' }}
                                                       {{ $isLocked ? 'cell-locked' : '' }}">
                                            <option value="">—</option>
                                            <option value="Pass"
                                                {{ ($cell->pass_fail ?? '') === 'Pass' ? 'selected' : '' }}>
                                                Pass
                                            </option>
                                            <option value="Fail"
                                                {{ ($cell->pass_fail ?? '') === 'Fail' ? 'selected' : '' }}>
                                                Fail
                                            </option>
                                        </select>
                                    @endif
                                </td>
                            @endforeach

                            {{-- Row average --}}
                            @if ($showRowAverage)
                                <td class="report-td text-center font-semibold
                                           text-blue-700 bg-blue-50 border-l-2
                                           border-blue-200"
                                    id="row-avg-{{ $rowIndex }}">
                                    —
                                </td>
                            @endif

                            {{-- Rank --}}
                            @if ($showRank)
                                <td class="report-td text-center font-bold
                                           text-yellow-700 bg-yellow-50 border-l
                                           border-yellow-200">
                                    {{ $matrix[$enrollment->id]['rank'] ?? '—' }}
                                </td>
                            @endif

                        </tr>
                    @endforeach
                </tbody>

                {{-- ── Column Averages Footer ── --}}
                @if ($showRowAverage)
                    <tfoot class="border-t-2 border-gray-300 bg-gray-50">
                        <tr>
                            <td colspan="2"
                                class="report-td font-semibold text-gray-500
                                       sticky left-0 bg-gray-50 z-10
                                       border-r-2 border-gray-300">
                                Column Avg
                            </td>
                            @foreach ($subjects as $colIndex => $subject)
                                <td class="report-td text-center font-bold
                                           text-blue-700 border-r border-gray-200"
                                    id="col-avg-{{ $colIndex }}">
                                    —
                                </td>
                            @endforeach
                            <td class="report-td bg-blue-50"></td>
                            @if ($showRank)
                                <td class="report-td bg-yellow-50"></td>
                            @endif
                        </tr>
                    </tfoot>
                @endif

            </table>
        </div>

        {{-- ── Save Bar ── --}}
        @if (! $isLocked)
            <div class="mt-4 flex items-center justify-between bg-white
                        rounded-lg shadow-sm border border-gray-200 px-5 py-3">
                <div class="flex items-center gap-4 text-xs text-gray-500">
                    <span id="modifiedCount" class="font-medium text-blue-600">
                        0 modified
                    </span>
                    <span id="filledCount">
                        0 / {{ $enrollments->count() * $subjects->count() }} filled
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button"
                            id="fillZeroBtn"
                            onclick="SheetManager.fillEmpty()"
                            class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs
                                   font-medium rounded hover:bg-gray-200">
                        Fill Empty → 0
                    </button>
                    <button type="button"
                            id="resetBtn"
                            onclick="SheetManager.resetModified()"
                            class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs
                                   font-medium rounded hover:bg-gray-200">
                        Reset Changes
                    </button>
                    <button type="submit"
                            id="saveBtn"
                            class="px-6 py-2 bg-blue-600 text-white text-sm
                                   font-medium rounded-md hover:bg-blue-700
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        Save Report
                    </button>
                </div>
            </div>
        @else
            <div class="mt-3 px-4 py-2 bg-red-50 border border-red-200
                        rounded text-xs text-red-700 flex items-center gap-2">
                🔒 This report is locked. Contact the administrator to unlock.
            </div>
        @endif

    </form>
</div>