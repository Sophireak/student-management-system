@extends('layouts.admin', ['title' => 'Ranking Report'])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $isAdmin     = auth()->user()->isAdmin();
@endphp

<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-700">Ranking Report</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Select class and period to generate a ranked student report.
    </p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-xl">
    <form method="GET"
          action="{{ route($routePrefix . '.reports.ranking.sheet') }}"
          id="filter-form">

        @if ($isAdmin)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year</label>
                <select name="academic_year_id" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option value="">— Select Year —</option>
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $year->is_active ? 'selected' : '' }}>
                            {{ $year->name }} @if($year->is_active)(Active)@endif
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">Class</label>
            <select name="class_id" id="sel-class" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— Select Class —</option>
                @foreach ($classes as $cls)
                    <option value="{{ $cls->id }}">
                        {{ $cls->name }} ({{ $cls->grade->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
            <select name="period" id="sel-period" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400">
                <option value="">— Select Period —</option>
                <optgroup label="Monthly">
                    @foreach ([1=>'September',2=>'October',3=>'November',4=>'December',5=>'January',6=>'February',7=>'March',8=>'April',9=>'May'] as $n => $name)
                        <option value="month_{{ $n }}">Month {{ $n }} — {{ $name }}</option>
                    @endforeach
                </optgroup>
                <optgroup label="Semester">
                    <option value="semester_1">Semester 1 (Sep – Jan)</option>
                    <option value="semester_2">Semester 2 (Feb – May)</option>
                </optgroup>
            </select>
        </div>

        <button type="submit"
                class="w-full px-5 py-2.5 bg-blue-600 text-white text-sm font-medium
                       rounded-md hover:bg-blue-700">
            Generate Ranking Report →
        </button>
    </form>
</div>

@endsection