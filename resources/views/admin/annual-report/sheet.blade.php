@extends('layouts.admin', ['title' => 'Annual Report — ' . $class->name])

@section('content')

{{-- Header --}}
<div class="mb-4 flex items-center justify-between flex-wrap gap-2">
    <div>
        <h2 class="text-lg font-semibold text-gray-700">Annual Report</h2>
        <p class="text-sm text-gray-400 mt-0.5">
            {{ $class->name }} · {{ $class->grade->name }}
            · {{ $academicYear->name }}
        </p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">

        {{-- Calculate button --}}
        @if (! $isLocked && ($hasSemester1 || $hasSemester2))
            <form method="POST"
                  action="{{ route('admin.annual-report.calculate') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="academic_year_id"
                       value="{{ $academicYearId }}">
                <button type="submit"
                        class="px-3 py-1.5 bg-green-600 text-white text-xs
                               font-medium rounded-md hover:bg-green-700"
                        onclick="return confirm('Calculate annual scores from semester data?')">
                    ⟳ Calculate from Semesters
                </button>
            </form>
        @endif

        {{-- Lock / Unlock --}}
        @if ($isLocked)
            <span class="px-3 py-1 text-xs font-semibold bg-red-100
                         text-red-700 rounded-full">🔒 Locked</span>
            <form method="POST"
                  action="{{ route('admin.annual-report.unlock') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="academic_year_id"
                       value="{{ $academicYearId }}">
                <button type="submit"
                        class="px-3 py-1 bg-yellow-100 text-yellow-700
                               text-xs rounded hover:bg-yellow-200">
                    Unlock
                </button>
            </form>
        @else
            <form method="POST"
                  action="{{ route('admin.annual-report.lock') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="academic_year_id"
                       value="{{ $academicYearId }}">
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
@include('partials.annual-report-filters', [
    'classes'         => $classes,
    'academicYears'   => $academicYears,
    'routePrefix'     => 'admin',
    'selectedYearId'  => $academicYearId,
    'selectedClassId' => $class->id,
])

{{-- Warnings --}}
@if (! $hasSemester1 || ! $hasSemester2)
    <div class="mb-4 px-4 py-3 bg-yellow-50 border border-yellow-200
                text-yellow-800 rounded-md text-sm">
        ⚠️
        @if (! $hasSemester1 && ! $hasSemester2)
            Neither semester report has been calculated yet.
        @elseif (! $hasSemester1)
            Semester 1 data is missing.
        @else
            Semester 2 data is missing.
        @endif
        Complete semester reports first.
    </div>
@endif

@if ($enrollments->isEmpty())
    <div class="bg-white rounded-lg border border-gray-200 px-5 py-8
                text-center text-gray-400 text-sm">
        No active students in this class.
    </div>
@else

    <form method="POST" action="{{ route('admin.annual-report.save') }}">
        @csrf
        <input type="hidden" name="class_id"          value="{{ $class->id }}">
        <input type="hidden" name="academic_year_id"  value="{{ $academicYearId }}">

        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">

                    {{-- Info row --}}
                    <tr class="bg-blue-50">
                        <td colspan="11"
                            class="px-4 py-2 text-xs text-blue-700">
                            <strong>Annual Report</strong>
                            · {{ $class->grade->name }}
                            · Class {{ $class->name }}
                            · {{ $academicYear->name }}
                            @if ($isLocked)
                                · <span class="text-red-600 font-semibold">
                                    LOCKED
                                  </span>
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
                                   w-44 sticky left-8 bg-gray-50 z-10">
                            Student Name
                        </th>

                        {{-- Semester 1 --}}
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-blue-600 uppercase border-r border-blue-100
                                   bg-blue-50 min-w-20">
                            S1 Avg
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-blue-600 uppercase border-r border-gray-200
                                   bg-blue-50 min-w-32">
                            S1 Conduct
                        </th>

                        {{-- Semester 2 --}}
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-purple-600 uppercase border-r border-purple-100
                                   bg-purple-50 min-w-20">
                            S2 Avg
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-purple-600 uppercase border-r border-gray-200
                                   bg-purple-50 min-w-32">
                            S2 Conduct
                        </th>

                        {{-- Annual --}}
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-700 uppercase border-r border-gray-200
                                   bg-gray-100 min-w-24">
                            Final Score
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-700 uppercase border-r border-gray-200
                                   bg-gray-100 min-w-16">
                            Rank
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-700 uppercase border-r border-gray-200
                                   bg-gray-100 min-w-16">
                            Pass
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold
                                   text-gray-700 uppercase border-r border-gray-200
                                   min-w-16">
                            Override
                        </th>
                        <th class="px-3 py-3 text-left text-xs font-semibold
                                   text-gray-500 uppercase min-w-36">
                            Notes
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($enrollments as $rowIndex => $enrollment)
                        @php
                            $row       = $existing->get($enrollment->id);
                            $isOverride = $row?->is_manual_override ?? false;
                            $rowClass  = $row
                                ? ($isOverride
                                    ? 'bg-yellow-50'
                                    : 'bg-green-50')
                                : '';
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

                            <input type="hidden"
                                   name="scores[{{ $rowIndex }}][enrollment_id]"
                                   value="{{ $enrollment->id }}">

                            {{-- S1 Average --}}
                            <td class="px-1 py-1 text-center border-r border-blue-100
                                       bg-blue-50">
                                <input type="number"
                                       name="scores[{{ $rowIndex }}][semester1_avg]"
                                       value="{{ $row?->semester1_avg }}"
                                       min="0" max="100" step="0.01"
                                       placeholder="—"
                                       {{ $isLocked ? 'readonly' : '' }}
                                       class="w-20 text-center border border-blue-200
                                              rounded px-1 py-1 text-sm bg-blue-50
                                              focus:outline-none focus:ring-2
                                              focus:ring-blue-400
                                              {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">
                            </td>

                            {{-- S1 Conduct --}}
                            <td class="px-1 py-1 text-center border-r border-gray-200
                                       bg-blue-50">
                                <select name="scores[{{ $rowIndex }}][semester1_conduct]"
                                        {{ $isLocked ? 'disabled' : '' }}
                                        class="w-36 border border-blue-200 rounded
                                               px-1 py-1 text-xs bg-blue-50
                                               focus:outline-none focus:ring-2
                                               focus:ring-blue-400">
                                    <option value="">—</option>
                                    @foreach (['Good','Satisfactory','Needs Improvement'] as $g)
                                        <option value="{{ $g }}"
                                            {{ $row?->semester1_conduct === $g ? 'selected' : '' }}>
                                            {{ $g }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- S2 Average --}}
                            <td class="px-1 py-1 text-center border-r border-purple-100
                                       bg-purple-50">
                                <input type="number"
                                       name="scores[{{ $rowIndex }}][semester2_avg]"
                                       value="{{ $row?->semester2_avg }}"
                                       min="0" max="100" step="0.01"
                                       placeholder="—"
                                       {{ $isLocked ? 'readonly' : '' }}
                                       class="w-20 text-center border border-purple-200
                                              rounded px-1 py-1 text-sm bg-purple-50
                                              focus:outline-none focus:ring-2
                                              focus:ring-purple-400
                                              {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">
                            </td>

                            {{-- S2 Conduct --}}
                            <td class="px-1 py-1 text-center border-r border-gray-200
                                       bg-purple-50">
                                <select name="scores[{{ $rowIndex }}][semester2_conduct]"
                                        {{ $isLocked ? 'disabled' : '' }}
                                        class="w-36 border border-purple-200 rounded
                                               px-1 py-1 text-xs bg-purple-50
                                               focus:outline-none focus:ring-2
                                               focus:ring-purple-400">
                                    <option value="">—</option>
                                    @foreach (['Good','Satisfactory','Needs Improvement'] as $g)
                                        <option value="{{ $g }}"
                                            {{ $row?->semester2_conduct === $g ? 'selected' : '' }}>
                                            {{ $g }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Final Score (calculated) --}}
                            <td class="px-3 py-2 text-center bg-gray-100
                                       font-bold text-gray-800 text-sm border-r
                                       border-gray-200">
                                {{ $row?->final_score ?? '—' }}
                            </td>

                            {{-- Rank --}}
                            <td class="px-3 py-2 text-center bg-gray-100
                                       font-bold text-blue-700 text-sm border-r
                                       border-gray-200">
                                {{ $row?->rank ?? '—' }}
                            </td>

                            {{-- Pass/Fail indicator --}}
                            <td class="px-3 py-2 text-center bg-gray-100
                                       text-sm border-r border-gray-200">
                                @if ($row?->is_passing === true)
                                    <span class="text-green-600 font-bold">✓</span>
                                @elseif ($row?->is_passing === false)
                                    <span class="text-red-600 font-bold">✗</span>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- Manual pass override checkbox --}}
                            <td class="px-3 py-2 text-center border-r border-gray-200">
                                @if (! $isLocked)
                                    <input type="checkbox"
                                           name="scores[{{ $rowIndex }}][is_passing]"
                                           value="1"
                                           {{ $row?->is_passing ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded"
                                           title="Override pass/fail">
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Notes --}}
                            <td class="px-1 py-1">
                                <input type="text"
                                       name="scores[{{ $rowIndex }}][notes]"
                                       value="{{ $row?->notes }}"
                                       placeholder="e.g. Repeat Grade"
                                       {{ $isLocked ? 'readonly' : '' }}
                                       class="w-full border border-gray-200 rounded
                                              px-2 py-1 text-xs text-gray-600
                                              focus:outline-none focus:ring-1
                                              focus:ring-blue-400
                                              {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">
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
            <span>Pass threshold: {{ $passThreshold }}</span>
        </div>

        @if (! $isLocked)
            <div class="mt-4 flex items-center justify-between bg-white
                        rounded-lg shadow-sm border border-gray-200 px-5 py-4">
                <p class="text-xs text-gray-400">
                    Final score and rank recalculate automatically on save.
                </p>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white text-sm
                               font-medium rounded-md hover:bg-blue-700">
                    Save Annual Report
                </button>
            </div>
        @else
            <div class="mt-4 px-5 py-3 bg-red-50 border border-red-200
                        rounded-lg text-sm text-red-700">
                🔒 This annual report is locked.
            </div>
        @endif
    </form>
@endif

@endsection