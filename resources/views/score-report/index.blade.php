@extends('layouts.admin', ['title' => 'Score Report'])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
@endphp

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Score Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">Search by year, class, and period to generate a report.</p>
</div>

{{-- Filter Form --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-4 mb-6">
    <form method="GET"
          action="{{ route($routePrefix . '.score-report.show') }}"
          class="flex flex-wrap items-end gap-4">

        {{-- Academic Year --}}
        <div>
            <label class="block text-xs text-gray-500 mb-1">Academic Year</label>
            <select name="academic_year_id"
                    required
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400 min-w-40">
                <option value="">— Select Year —</option>
                @foreach ($academicYears as $year)
                    <option value="{{ $year->id }}"
                        {{ isset($academicYear) && $academicYear->id === $year->id ? 'selected' : '' }}>
                        {{ $year->name }}
                        @if ($year->is_active) (Active) @endif
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Class --}}
        <div>
            <label class="block text-xs text-gray-500 mb-1">Class</label>
            <select name="class_id"
                    required
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400 min-w-40">
                <option value="">— Select Class —</option>
                @foreach ($classes as $cls)
                    <option value="{{ $cls->id }}"
                        {{ isset($class) && $class->id === $cls->id ? 'selected' : '' }}>
                        {{ $cls->name }} ({{ $cls->grade->name }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Period --}}
        <div>
            <label class="block text-xs text-gray-500 mb-1">Period</label>
            <select name="period"
                    required
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400 min-w-48">
                <option value="">— Select Period —</option>
                <optgroup label="Monthly">
                    @foreach ([1=>'September',2=>'October',3=>'November',4=>'December',5=>'January',6=>'February',7=>'March',8=>'April',9=>'May'] as $num => $name)
                        <option value="month_{{ $num }}"
                            {{ isset($type, $value) && $type === 'month' && $value === $num ? 'selected' : '' }}>
                            Month {{ $num }} — {{ $name }}
                        </option>
                    @endforeach
                </optgroup>
                <optgroup label="Semester">
                    <option value="semester_1"
                        {{ isset($type, $value) && $type === 'semester' && $value === 1 ? 'selected' : '' }}>
                        Semester 1 (Sep – Jan)
                    </option>
                    <option value="semester_2"
                        {{ isset($type, $value) && $type === 'semester' && $value === 2 ? 'selected' : '' }}>
                        Semester 2 (Feb – May)
                    </option>
                </optgroup>
            </select>
        </div>

        <div>
            <button type="submit"
                    class="px-5 py-1.5 bg-blue-600 text-white text-sm font-medium
                           rounded hover:bg-blue-700 transition-colors">
                Search
            </button>
        </div>

    </form>
</div>

{{-- Report Table --}}
@if (isset($rows))

    <div class="mb-3 flex items-center justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-700">
                Score Report — {{ $periodLabel }}
            </h3>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $class->name }} · {{ $class->grade->name }} · {{ $academicYear->name }}
            </p>
        </div>
        <button onclick="window.print()"
                class="px-4 py-1.5 text-sm bg-gray-100 border border-gray-200
                       text-gray-600 rounded hover:bg-gray-200 transition-colors">
            🖨️ Print
        </button>
    </div>

    @if (count($rows) === 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-8
                    text-center text-gray-400 text-sm">
            No active students found in this class.
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-10">
                            No.
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-24">
                            ID
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase border-r border-gray-200">
                            Name
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-16">
                            Sex
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-24">
                            Total
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-24">
                            Average
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase border-r border-gray-200 w-16">
                            Grade
                        </th>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-20">
                            Remark
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($rows as $i => $row)
                        <tr class="hover:bg-gray-50 {{ $row['passed'] === false ? 'bg-red-50' : '' }}">
                            <td class="px-3 py-2 text-center text-xs text-gray-400 border-r border-gray-200">
                                {{ $i + 1 }}
                            </td>
                            <td class="px-3 py-2 text-center text-xs text-gray-500 border-r border-gray-200">
                                {{ $row['student']->student_id ?? '—' }}
                            </td>
                            <td class="px-4 py-2 font-medium text-gray-800 border-r border-gray-200 whitespace-nowrap">
                                {{ $row['student']->full_name }}
                            </td>
                            <td class="px-3 py-2 text-center text-xs text-gray-500 border-r border-gray-200">
                                {{ $row['student']->gender ?? '—' }}
                            </td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700 border-r border-gray-200">
                                {{ $row['total'] }}
                            </td>
                            <td class="px-3 py-2 text-center text-sm text-gray-700 border-r border-gray-200">
                                {{ $row['average'] }}
                            </td>
                            <td class="px-3 py-2 text-center border-r border-gray-200">
                                @php
                                    $gradeColors = [
                                        'A' => 'bg-green-100 text-green-700',
                                        'B' => 'bg-blue-100 text-blue-700',
                                        'C' => 'bg-yellow-100 text-yellow-700',
                                        'D' => 'bg-orange-100 text-orange-700',
                                        'F' => 'bg-red-100 text-red-700',
                                    ];
                                    $gc = $gradeColors[$row['grade']] ?? 'bg-gray-100 text-gray-500';
                                @endphp
                                <span class="px-2 py-0.5 rounded text-xs font-semibold {{ $gc }}">
                                    {{ $row['grade'] }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <span class="px-2 py-0.5 rounded text-xs font-semibold
                                    {{ $row['remark'] === 'Pass' ? 'bg-green-100 text-green-700' :
                                       ($row['remark'] === 'Fail' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-400') }}">
                                    {{ $row['remark'] }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-xs text-gray-500 font-medium">
                            Total Students: {{ count($rows) }}
                        </td>
                        <td colspan="2" class="px-3 py-2 text-center text-xs text-gray-500">
                            Passed:
                            <span class="font-semibold text-green-700">
                                {{ collect($rows)->where('passed', true)->count() }}
                            </span>
                            &nbsp;/&nbsp;
                            Failed:
                            <span class="font-semibold text-red-700">
                                {{ collect($rows)->where('passed', false)->count() }}
                            </span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endif

@endif

@endsection