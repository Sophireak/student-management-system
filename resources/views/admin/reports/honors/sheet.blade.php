@extends('layouts.admin', ['title' => 'Honors Report — ' . $class->name])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $isAdmin     = auth()->user()->isAdmin();
@endphp

{{-- Header --}}
<div class="mb-4 flex items-center justify-between flex-wrap gap-2">
    <div>
        <a href="{{ route($routePrefix . '.reports.honors.index') }}"
           class="text-sm text-blue-600 hover:underline">← Honors Report</a>
        <h2 class="text-lg font-semibold text-gray-700 mt-1">
            Top {{ $topN }} Students · {{ $class->name }} · {{ $class->grade->name }}
        </h2>
        <p class="text-sm text-gray-400">{{ $periodLabel }} · {{ $academicYear->name }}</p>
    </div>
    <button onclick="window.print()"
            class="px-4 py-2 bg-gray-700 text-white text-sm rounded-md hover:bg-gray-800 print:hidden">
        🖨️ Print / PDF
    </button>
</div>

{{-- Filter bar --}}
<div class="mb-4 bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3 print:hidden">
    <form method="GET"
          action="{{ route($routePrefix . '.reports.honors.sheet') }}"
          class="flex flex-wrap items-end gap-3">

        @if ($isAdmin)
            <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
        @endif

        <div>
            <label class="block text-xs text-gray-500 mb-1">Class</label>
            <select name="class_id"
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
                @foreach ($classes as $cls)
                    <option value="{{ $cls->id }}" {{ $cls->id === $class->id ? 'selected' : '' }}>
                        {{ $cls->name }} ({{ $cls->grade->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Period</label>
            <select name="period"
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
                <optgroup label="Monthly">
                    @foreach ([1=>'September',2=>'October',3=>'November',4=>'December',5=>'January',6=>'February',7=>'March',8=>'April',9=>'May'] as $n => $name)
                        <option value="month_{{ $n }}"
                            {{ $selectedPeriod === 'month_'.$n ? 'selected' : '' }}>
                            Month {{ $n }} — {{ $name }}
                        </option>
                    @endforeach
                </optgroup>
                <optgroup label="Semester">
                    <option value="semester_1" {{ $selectedPeriod === 'semester_1' ? 'selected' : '' }}>
                        Semester 1 (Sep – Jan)
                    </option>
                    <option value="semester_2" {{ $selectedPeriod === 'semester_2' ? 'selected' : '' }}>
                        Semester 2 (Feb – May)
                    </option>
                </optgroup>
            </select>
        </div>

        <div>
            <label class="block text-xs text-gray-500 mb-1">Top N</label>
            <select name="top"
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="3"  {{ $topN == 3  ? 'selected' : '' }}>Top 3</option>
                <option value="5"  {{ $topN == 5  ? 'selected' : '' }}>Top 5</option>
                <option value="10" {{ $topN == 10 ? 'selected' : '' }}>Top 10</option>
            </select>
        </div>

        <button type="submit"
                class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
            Go
        </button>
    </form>
</div>

{{-- Print header --}}
<div class="hidden print:block text-center mb-8">
    <p class="text-sm font-semibold">Kingdom of Cambodia</p>
    <p class="text-sm">Nation – Religion – King</p>
    <div class="mt-4 text-lg font-bold uppercase tracking-wide">
        🏆 Honors Roll
    </div>
    <div class="text-sm mt-1">
        {{ $class->grade->name }} · Class {{ $class->name }}
        · {{ $periodLabel }} · {{ $academicYear->name }}
    </div>
    <div class="text-sm text-gray-500 mt-1">Top {{ $topN }} Students</div>
</div>

@if ($rows->isEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-8
                text-center text-gray-400 text-sm">
        No score data found for this period.
    </div>
@else
    {{-- Podium display for top 3 --}}
    @if ($rows->count() >= 3)
        <div class="mb-6 flex items-end justify-center gap-4 print:hidden">
            {{-- 2nd place --}}
            <div class="text-center">
                <div class="text-3xl mb-1">🥈</div>
                <div class="bg-gray-100 border border-gray-200 rounded-lg px-4 py-3 w-36 h-24 flex flex-col items-center justify-center">
                    <div class="text-xs font-semibold text-gray-700 text-center leading-tight">
                        {{ $rows[1]['enrollment']->student->full_name }}
                    </div>
                    <div class="text-lg font-bold text-gray-600 mt-1">
                        {{ $rows[1]['average'] ?? '—' }}
                    </div>
                    <div class="text-xs text-gray-400">Rank 2</div>
                </div>
            </div>
            {{-- 1st place --}}
            <div class="text-center -mb-4">
                <div class="text-4xl mb-1">🥇</div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3 w-40 h-28 flex flex-col items-center justify-center shadow">
                    <div class="text-xs font-bold text-yellow-800 text-center leading-tight">
                        {{ $rows[0]['enrollment']->student->full_name }}
                    </div>
                    <div class="text-xl font-bold text-yellow-700 mt-1">
                        {{ $rows[0]['average'] ?? '—' }}
                    </div>
                    <div class="text-xs text-yellow-600">Rank 1 🌟</div>
                </div>
            </div>
            {{-- 3rd place --}}
            <div class="text-center">
                <div class="text-3xl mb-1">🥉</div>
                <div class="bg-orange-50 border border-orange-200 rounded-lg px-4 py-3 w-36 h-24 flex flex-col items-center justify-center">
                    <div class="text-xs font-semibold text-orange-800 text-center leading-tight">
                        {{ $rows[2]['enrollment']->student->full_name }}
                    </div>
                    <div class="text-lg font-bold text-orange-700 mt-1">
                        {{ $rows[2]['average'] ?? '—' }}
                    </div>
                    <div class="text-xs text-orange-500">Rank 3</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Full honors table --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden print:shadow-none print:border-0">
        <table class="min-w-full text-sm print:text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-16">Rank</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-8">No.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase border-r border-gray-200">Student Name</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-20">Gender</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-20">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-20">Average</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-16">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($rows as $i => $row)
                    @php
                        $rankVal   = $row['rank'];
                        $rowClass  = match($rankVal) {
                            1 => 'bg-yellow-50',
                            2 => 'bg-gray-50',
                            3 => 'bg-orange-50',
                            default => 'hover:bg-gray-50',
                        };
                    @endphp
                    <tr class="{{ $rowClass }}">
                        <td class="px-4 py-3 text-center font-bold border-r border-gray-200 text-lg">
                            @if ($rankVal == 1) 🥇
                            @elseif ($rankVal == 2) 🥈
                            @elseif ($rankVal == 3) 🥉
                            @else <span class="text-gray-500 text-sm">{{ $rankVal }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-400 border-r border-gray-200">
                            {{ $i + 1 }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-800 border-r border-gray-200 whitespace-nowrap">
                            {{ $row['enrollment']->student->full_name }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500 border-r border-gray-200 capitalize text-xs">
                            {{ $row['enrollment']->student->gender ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center font-semibold text-gray-700 border-r border-gray-200">
                            {{ $row['total'] ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-gray-800 border-r border-gray-200">
                            {{ $row['average'] ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center font-bold text-lg
                            {{ $row['letter_grade'] === 'A' ? 'text-green-600' :
                               ($row['letter_grade'] === 'B' ? 'text-blue-600' :
                               ($row['letter_grade'] === 'C' ? 'text-yellow-600' :
                               ($row['letter_grade'] === 'D' ? 'text-orange-600' :
                               ($row['letter_grade'] === 'E' ? 'text-red-600' : 'text-gray-400')))) }}">
                            {{ $row['letter_grade'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Print signature area --}}
    <div class="hidden print:flex print:justify-between print:mt-16 print:px-8 text-sm text-gray-600">
        <div class="text-center">
            <div>Class Teacher</div>
            <div class="mt-12 border-t border-gray-400 pt-1">Signature / Name</div>
        </div>
        <div class="text-center">
            <div>School Principal</div>
            <div class="mt-12 border-t border-gray-400 pt-1">Signature / Name</div>
        </div>
    </div>
@endif

@push('styles')
<style>
@media print {
    .print\:hidden { display: none !important; }
    .print\:block  { display: block !important; }
    .print\:flex   { display: flex !important; }
    nav, aside, header { display: none !important; }
    @page { margin: 1.5cm; }
}
</style>
@endpush

@endsection