@extends('layouts.admin', ['title' => 'Ranking Report — ' . $class->name])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $isAdmin     = auth()->user()->isAdmin();
    $linkClass   = $isAdmin ? 'text-sm text-blue-600 hover:underline' : 'inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700';
    $barRadius   = $isAdmin ? 'rounded-lg shadow-sm' : 'rounded-2xl shadow-sm';
    $selRadius   = $isAdmin ? 'rounded' : 'rounded-full';
    $selRing     = $isAdmin ? 'focus:ring-blue-400' : 'focus:ring-green-500 focus:border-green-500';
@endphp

{{-- Header --}}
<div class="{{ $isAdmin ? '' : 'bg-white rounded-2xl border border-gray-200 p-4 shadow-sm' }} mb-4 flex items-center justify-between flex-wrap gap-2">
    <div>
        <a href="{{ route($routePrefix . '.reports.ranking.index') }}"
           class="{{ $linkClass }}">
            @unless ($isAdmin)<i class="ti ti-arrow-left text-base"></i>@endunless
            {{ $isAdmin ? '← Ranking Report' : 'Ranking Report' }}
        </a>
        <h2 class="text-lg font-semibold text-gray-700 mt-1">
            {{ $class->name }} · {{ $class->grade->name }}
        </h2>
        <p class="text-sm text-gray-400">{{ $periodLabel }} · {{ $academicYear->name }}</p>
    </div>
    <button onclick="window.print()"
            class="flex items-center gap-2 px-4 py-2 bg-gray-700 text-white text-sm {{ $isAdmin ? 'rounded-md' : 'rounded-full' }} hover:bg-gray-800 print:hidden">
        <i class="ti ti-printer text-base"></i> Print / PDF
    </button>
</div>

{{-- Filter bar --}}
<div class="mb-4 bg-white {{ $barRadius }} border border-gray-200 px-4 py-3 print:hidden">
    <form method="GET"
          action="{{ route($routePrefix . '.reports.ranking.sheet') }}"
          class="flex flex-wrap items-end gap-3">

        @if ($isAdmin)
            <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">
        @endif

        <div>
            <label class="block text-xs text-gray-500 mb-1">Class</label>
            <select name="class_id"
                    class="border border-gray-300 {{ $selRadius }} px-2 py-1.5 text-sm focus:outline-none focus:ring-2 {{ $selRing }}">
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
                    class="border border-gray-300 {{ $selRadius }} px-2 py-1.5 text-sm focus:outline-none focus:ring-2 {{ $selRing }}">
                <optgroup label="Monthly">
                    @foreach ([1=>'September',2=>'October',3=>'November',4=>'December',5=>'January',6=>'February',7=>'March',8=>'April',9=>'May'] as $n => $name)
                        <option value="month_{{ $n }}" {{ $selectedPeriod === 'month_'.$n ? 'selected' : '' }}>
                            Month {{ $n }} — {{ $name }}
                        </option>
                    @endforeach
                </optgroup>
                <optgroup label="Semester">
                    <option value="semester_1" {{ $selectedPeriod === 'semester_1' ? 'selected' : '' }}>Semester 1 (Sep – Jan)</option>
                    <option value="semester_2" {{ $selectedPeriod === 'semester_2' ? 'selected' : '' }}>Semester 2 (Feb – May)</option>
                </optgroup>
            </select>
        </div>

        <button type="submit"
                class="px-4 py-1.5 {{ $isAdmin ? 'bg-blue-600 hover:bg-blue-700 rounded' : 'bg-green-600 hover:bg-green-700 rounded-full' }} text-white text-sm">
            Go
        </button>
    </form>
</div>

{{-- Print header --}}
<div class="hidden print:block text-center mb-6">
    <p class="text-sm font-semibold">Kingdom of Cambodia</p>
    <p class="text-sm">Nation – Religion – King</p>
    <div class="mt-3 text-base font-bold">Student Ranking Report</div>
    <div class="text-sm mt-1">
        {{ $class->grade->name }} · Class {{ $class->name }} · {{ $periodLabel }} · {{ $academicYear->name }}
    </div>
</div>

@if ($rows->isEmpty())
    <div class="bg-white {{ $barRadius }} border border-gray-200 px-5 py-8 text-center text-gray-400 text-sm">
        No score data found for this period.
    </div>
@else
    <div class="bg-white {{ $isAdmin ? 'rounded-lg' : 'rounded-2xl' }} shadow-sm border border-gray-200 overflow-x-auto print:shadow-none print:border-0">
        <table class="min-w-full text-sm print:text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-10">Rank</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-8">No.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 min-w-40">Student Name</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-16">Gender</th>
                    @foreach ($subjects as $subject)
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-100 min-w-20">
                            {{ $subject->name }}
                        </th>
                    @endforeach
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-100 w-16">Total</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-100 w-16">Average</th>
                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-14">Grade</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($rows as $i => $row)
                    @php
                        $rankVal = $row['rank'];
                        $rankClass = match($rankVal) {
                            1 => 'bg-yellow-50 font-bold',
                            2 => 'bg-gray-50 font-semibold',
                            3 => 'bg-orange-50 font-semibold',
                            default => '',
                        };
                    @endphp
                    <tr class="hover:bg-blue-50 {{ $rankClass }}">
                        <td class="px-3 py-2 text-center border-r border-gray-200">
                            @if ($rankVal == 1) 🥇
                            @elseif ($rankVal == 2) 🥈
                            @elseif ($rankVal == 3) 🥉
                            @else <span class="text-gray-500">{{ $rankVal }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-center text-gray-400 text-xs border-r border-gray-200">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 font-medium text-gray-800 border-r border-gray-200 whitespace-nowrap">
                            {{ $row['enrollment']->student->full_name }}
                        </td>
                        <td class="px-3 py-2 text-center text-gray-500 border-r border-gray-200 capitalize text-xs">
                            {{ $row['enrollment']->student->gender ?? '—' }}
                        </td>
                        @foreach ($subjects as $subject)
                            <td class="px-3 py-2 text-center text-gray-700 border-r border-gray-100">
                                {{ $row['subject_scores'][$subject->id] ?? '—' }}
                            </td>
                        @endforeach
                        <td class="px-3 py-2 text-center font-semibold text-gray-700 border-r border-gray-100">
                            {{ $row['total'] ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center font-semibold text-gray-700 border-r border-gray-100">
                            {{ $row['average'] ?? '—' }}
                        </td>
                        <td class="px-3 py-2 text-center font-bold
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

    {{-- Grade legend --}}
    <div class="mt-3 flex flex-wrap gap-3 text-xs text-gray-500 print:mt-6">
        <span><strong class="text-green-600">A</strong> = 80–100</span>
        <span><strong class="text-blue-600">B</strong> = 70–79</span>
        <span><strong class="text-yellow-600">C</strong> = 60–69</span>
        <span><strong class="text-orange-600">D</strong> = 50–59</span>
        <span><strong class="text-red-600">E</strong> = Below 50</span>
        <span class="ml-auto text-gray-400">Total students: {{ $rows->count() }}</span>
    </div>
@endif

@push('styles')
<style>
@media print {
    body * { visibility: hidden; }
    .print\:block, .print\:block * { visibility: visible; }
    table, table * { visibility: visible; }
    .bg-white, .bg-white * { visibility: visible; }
    #app, #app * { visibility: visible; }
    .print\:hidden { display: none !important; }
    @page { margin: 1.5cm; }
}
</style>
@endpush

@endsection