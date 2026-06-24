@extends('layouts.admin', ['title' => 'Ranking Report'])

@section('content')

{{-- Page Header --}}


@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $isAdmin     = auth()->user()->isAdmin();
@endphp

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Ranking Report</h1>
    <p class="text-sm text-gray-500 mt-1">View student rankings by class and period.</p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-xl">
    <form method="GET"
          action="{{ route($routePrefix . '.reports.ranking.sheet') }}"
          id="filter-form">

            <!-- {{-- Class --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <select name="class_id" required
                            class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">— Select Class —</option>
                        @foreach ($classes as $cls)
                            <option value="{{ $cls->id }}">
                                {{ $cls->name }} ({{ $cls->grade->name }}) · {{ $cls->academicYear->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div> -->



@if ($isAdmin)
            {{-- Academic Year --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <select name="academic_year_id" required
                            class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">— Select Year —</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
@endif
            {{-- Period --}}
            <!-- <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Period <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="ti ti-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <select name="period" required
                            class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">— Select Period —</option>
                        <optgroup label="Monthly">
                            @foreach ([1=>'September',2=>'October',3=>'November',4=>'December',5=>'January',6=>'February',7=>'March',8=>'April',9=>'May'] as $num => $name)
                                <option value="month_{{ $num }}">Month {{ $num }} — {{ $name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Semester">
                            <option value="semester_1">Semester 1 (Sep — Jan)</option>
                            <option value="semester_2">Semester 2 (Feb — May)</option>
                        </optgroup>
                    </select>
                </div>
            </div> -->
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
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="ti ti-chart-bar text-base"></i> View Ranking
            </button>

        </form>
    </div>
</div>

@endsection
