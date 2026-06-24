@extends('layouts.admin', ['title' => 'Examination Scores — ' . $class->name])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $isAdmin     = auth()->user()->isAdmin();
@endphp

{{-- Header --}}
<div class="mb-6">
    <a href="{{ route($routePrefix . '.examination-scores.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Examination Scores
    </a>
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-clipboard-list text-green-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $class->name }} · {{ $class->grade->name }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ $semesterLabel }} · {{ $academicYear->name }}</p>
            </div>
        </div>

        {{-- Lock / Unlock --}}
        <div class="flex items-center gap-2">
            @if ($isLocked)
                <span class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold bg-red-100 text-red-700 rounded-lg border border-red-200">
                    <i class="ti ti-lock text-sm"></i> Locked
                </span>
                @if ($isAdmin)
                    <form method="POST" action="{{ route('admin.examination-scores.unlock') }}">
                        @csrf
                        <input type="hidden" name="class_id"         value="{{ $class->id }}">
                        <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                        <input type="hidden" name="period_type"      value="semester">
                        <input type="hidden" name="semester"         value="{{ $semester }}">
                        <button type="submit"
                                class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors">
                            <i class="ti ti-lock-open text-sm"></i> Unlock
                        </button>
                    </form>
                @endif
            @else
                @if ($isAdmin)
                    <form method="POST" action="{{ route('admin.examination-scores.lock') }}">
                        @csrf
                        <input type="hidden" name="class_id"         value="{{ $class->id }}">
                        <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
                        <input type="hidden" name="period_type"      value="semester">
                        <input type="hidden" name="semester"         value="{{ $semester }}">
                        <button type="submit"
                                class="flex items-center gap-2 px-3 py-1.5 text-xs font-medium bg-gray-50 border border-gray-200 text-gray-500 rounded-lg hover:bg-yellow-50 hover:border-yellow-300 hover:text-yellow-700 transition-colors">
                            <i class="ti ti-lock text-sm"></i> Lock Sheet
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>
</div>

{{-- Alert --}}
@if (session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif

{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET"
          action="{{ route($routePrefix . '.examination-scores.sheet') }}"
          id="filter-form"
          class="flex flex-wrap items-end gap-3">

        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Class</label>
            <div class="relative">
                <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <select name="class_id" id="sel-class"
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $cls->id === $class->id ? 'selected' : '' }}>
                            {{ $cls->name }} ({{ $cls->grade->name }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="min-w-36">
            <label class="block text-xs font-medium text-gray-500 mb-1">Period Type</label>
            <div class="relative">
                <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <select name="period_type" id="sel-period-type"
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="month">Monthly</option>
                    <option value="semester" selected>Semester</option>
                </select>
            </div>
        </div>

        <div id="group-month" class="hidden min-w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Month</label>
            <div class="relative">
                <i class="ti ti-calendar-month absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <select name="month" id="sel-month"
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    @foreach ([1=>'September',2=>'October',3=>'November',4=>'December',5=>'January',6=>'February',7=>'March',8=>'April',9=>'May'] as $n => $name)
                        <option value="{{ $n }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div id="group-semester" class="min-w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Semester</label>
            <div class="relative">
                <i class="ti ti-calendar-stats absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <select name="semester" id="sel-semester"
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="1" {{ $semester === 1 ? 'selected' : '' }}>Semester 1 (Sep – Jan)</option>
                    <option value="2" {{ $semester === 2 ? 'selected' : '' }}>Semester 2 (Feb – May)</option>
                </select>
            </div>
        </div>

    </form>
</div>

{{-- Score Sheet --}}
@if ($enrollments->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
        <i class="ti ti-users-off text-4xl text-gray-300 block mb-2"></i>
        <p class="text-gray-400 text-sm">No active students in this class.</p>
    </div>
@elseif ($subjects->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
        <i class="ti ti-book-off text-4xl text-gray-300 block mb-2"></i>
        <p class="text-gray-400 text-sm">No subjects configured for {{ $class->grade->name }}.</p>
    </div>
@else
    <form method="POST" action="{{ route($routePrefix . '.examination-scores.save-semester') }}">
        @csrf
        <input type="hidden" name="class_id"         value="{{ $class->id }}">
        <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
        <input type="hidden" name="semester"         value="{{ $semester }}">

        <div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-8 sticky left-0 bg-gray-50 z-10">#</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-48 sticky left-8 bg-gray-50 z-10">Student Name</th>
                        @foreach ($subjects as $subject)
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-100 min-w-24">
                                <div>{{ $subject->name }}</div>
                                <div class="text-gray-400 font-normal normal-case text-xs mt-0.5">
                                    @if ($subject->isNumeric())   /{{ $subject->max_score }}
                                    @elseif ($subject->isGrade()) Grade
                                    @else                         P/F
                                    @endif
                                </div>
                            </th>
                        @endforeach
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-100 min-w-20">Total</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-100 min-w-20">Avg</th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase min-w-16">Rank</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($enrollments as $rowIndex => $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-center text-xs text-gray-400 border-r border-gray-200 sticky left-0 bg-white z-10">{{ $rowIndex + 1 }}</td>
                            <td class="px-4 py-2 font-medium text-gray-800 border-r border-gray-200 whitespace-nowrap sticky left-8 bg-white z-10">
                                {{ $enrollment->student->full_name }}
                            </td>
                            @foreach ($subjects as $colIndex => $subject)
                                @php
                                    $existing = $matrix[$enrollment->id][$subject->id] ?? null;
                                    $inputKey = "{$rowIndex}_{$colIndex}";
                                    $hasValue = $existing !== null;
                                @endphp
                                <td class="px-1 py-1 text-center border-r border-gray-100">
                                    <input type="hidden" name="scores[{{ $inputKey }}][enrollment_id]" value="{{ $enrollment->id }}">
                                    <input type="hidden" name="scores[{{ $inputKey }}][subject_id]"    value="{{ $subject->id }}">

                                    @if ($subject->isNumeric())
                                        <input type="number"
                                               name="scores[{{ $inputKey }}][score]"
                                               value="{{ $existing?->score }}"
                                               min="0" max="{{ $subject->max_score }}" step="0.5"
                                               placeholder="—"
                                               {{ $isLocked ? 'readonly' : '' }}
                                               data-row="{{ $rowIndex }}" data-col="{{ $colIndex }}"
                                               class="score-input w-20 text-center border rounded-lg px-1 py-1 text-sm
                                                      focus:outline-none focus:ring-2 focus:ring-green-500
                                                      {{ $hasValue ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}
                                                      {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">
                                    @elseif ($subject->isGrade())
                                        <select name="scores[{{ $inputKey }}][grade]"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                data-row="{{ $rowIndex }}" data-col="{{ $colIndex }}"
                                                class="score-input w-36 border rounded-lg px-1 py-1 text-xs
                                                       focus:outline-none focus:ring-2 focus:ring-green-500
                                                       {{ $hasValue ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                                            <option value="">—</option>
                                            @foreach (['Good','Satisfactory','Needs Improvement'] as $g)
                                                <option value="{{ $g }}" {{ $existing?->grade === $g ? 'selected' : '' }}>{{ $g }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select name="scores[{{ $inputKey }}][pass_fail]"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                data-row="{{ $rowIndex }}" data-col="{{ $colIndex }}"
                                                class="score-input w-20 border rounded-lg px-1 py-1 text-xs
                                                       focus:outline-none focus:ring-2 focus:ring-green-500
                                                       {{ $hasValue ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                                            <option value="">—</option>
                                            <option value="Pass" {{ $existing?->pass_fail === 'Pass' ? 'selected' : '' }}>Pass</option>
                                            <option value="Fail" {{ $existing?->pass_fail === 'Fail' ? 'selected' : '' }}>Fail</option>
                                        </select>
                                    @endif
                                </td>
                            @endforeach
                            @php $sum = $summary[$enrollment->id] ?? []; @endphp
                            <td class="px-3 py-2 text-center text-sm text-gray-700 border-r border-gray-100">{{ $sum['total'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700 border-r border-gray-100">{{ $sum['average'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-center text-sm font-semibold text-gray-700">{{ $sum['rank'] ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if (! $isLocked)
            <div class="mt-4 flex items-center justify-between bg-white rounded-xl border border-gray-200 px-5 py-4">
                <p class="text-xs text-gray-400"><i class="ti ti-info-circle mr-1"></i>Green cells already saved. Empty cells skipped. Total, Average and Rank auto-calculated after save.</p>
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    <i class="ti ti-device-floppy text-base"></i> Save Scores
                </button>
            </div>
        @else
            <div class="mt-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-5 py-4 rounded-xl">
                <i class="ti ti-lock text-base"></i> This sheet has been locked by the administrator. No edits allowed.
            </div>
        @endif
    </form>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form      = document.getElementById('filter-form');
    const selClass  = document.getElementById('sel-class');
    const selPeriod = document.getElementById('sel-period-type');
    const selMonth  = document.getElementById('sel-month');
    const selSem    = document.getElementById('sel-semester');
    const grpMonth  = document.getElementById('group-month');
    const grpSem    = document.getElementById('group-semester');

    function togglePeriod() {
        const isMonth = selPeriod.value === 'month';
        grpMonth.classList.toggle('hidden', !isMonth);
        grpSem.classList.toggle('hidden', isMonth);
    }

    function trySubmit() {
        const classOk = selClass.value !== '';
        const monthOk = selPeriod.value === 'month' && selMonth.value !== '';
        const semOk   = selPeriod.value === 'semester' && selSem.value !== '';
        if (classOk && (monthOk || semOk)) form.submit();
    }

    selPeriod.addEventListener('change', function () { togglePeriod(); trySubmit(); });
    selClass.addEventListener('change', trySubmit);
    selMonth.addEventListener('change', trySubmit);
    selSem.addEventListener('change', trySubmit);

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
                    if (nextCol === totalCols - 1) nextRow = row - 1 >= 0 ? row - 1 : totalRows - 1;
                } else if (e.key === 'ArrowDown' || e.key === 'Enter') {
                    e.preventDefault();
                    nextRow = row + 1 < totalRows ? row + 1 : 0;
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    nextRow = row - 1 >= 0 ? row - 1 : totalRows - 1;
                } else { return; }

                const next = document.querySelector(`.score-input[data-row="${nextRow}"][data-col="${nextCol}"]`);
                if (next) { next.focus(); if (next.select) next.select(); }
            });
        }
        input.addEventListener('change', function () {
            const empty = this.value === '';
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
