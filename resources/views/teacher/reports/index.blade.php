@extends('layouts.teacher', ['title' => 'Reports'])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
@endphp

<div x-data="{
    reportType: 'score-list',
    period: 'monthly',
    classId: '',
    month: '2',
    semester: '1',
    year: ''
}">

    {{-- ========================================
         REPORT TYPE SELECTOR
         ======================================== --}}
    <div class="mb-6">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-3">
            <i class="ti ti-file-report text-base mr-1"></i>
            Select Report Type
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            {{-- Score List --}}
            <label class="cursor-pointer">
                <input type="radio" x-model="reportType" value="score-list" class="peer sr-only">
                <div class="border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 
                            rounded-2xl p-5 transition-all hover:border-gray-300">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
                            <i class="ti ti-clipboard-list text-blue-600 text-2xl"></i>
                        </div>
                        <div x-show="reportType === 'score-list'" 
                             class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                            <i class="ti ti-check text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 mb-1">Score List</h3>
                    <p class="text-xs text-gray-500">តារាងស្រង់ពិន្ទុ</p>
                    <p class="text-xs text-gray-400 mt-1">Full table with all subjects</p>
                </div>
            </label>

            {{-- Ranking --}}
            <label class="cursor-pointer">
                <input type="radio" x-model="reportType" value="ranking" class="peer sr-only">
                <div class="border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 
                            rounded-2xl p-5 transition-all hover:border-gray-300">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center">
                            <i class="ti ti-trophy text-purple-600 text-2xl"></i>
                        </div>
                        <div x-show="reportType === 'ranking'" 
                             class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                            <i class="ti ti-check text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 mb-1">Ranking</h3>
                    <p class="text-xs text-gray-500">តារាងចំណាត់ថ្នាក់</p>
                    <p class="text-xs text-gray-400 mt-1">Ranked list with grades</p>
                </div>
            </label>

            {{-- Honor --}}
            <label class="cursor-pointer">
                <input type="radio" x-model="reportType" value="honor" class="peer sr-only">
                <div class="border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 
                            rounded-2xl p-5 transition-all hover:border-gray-300">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center">
                            <i class="ti ti-medal text-amber-600 text-2xl"></i>
                        </div>
                        <div x-show="reportType === 'honor'" 
                             class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                            <i class="ti ti-check text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800 mb-1">Honor</h3>
                    <p class="text-xs text-gray-500">តារាងកិត្តិយស</p>
                    <p class="text-xs text-gray-400 mt-1">Top 5 students display</p>
                </div>
            </label>
        </div>
    </div>

    {{-- ========================================
         FILTERS
         ======================================== --}}
    <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
        <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider mb-4">
            <i class="ti ti-filter text-base mr-1"></i>
            Report Filters
        </h2>

        <form method="GET" action="{{ route($routePrefix . '.reports.print') }}" target="_blank">

            <input type="hidden" name="report" :value="reportType">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                {{-- Class --}}
<div>
    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
        Class <span class="text-red-500">*</span>
    </label>

    @if ($classes->count() === 1)
        {{-- Single class: auto-select + show as display --}}
        @php $singleClass = $classes->first(); @endphp
        <input type="hidden" name="class_id" value="{{ $singleClass->id }}">
        <div class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm text-gray-700 relative">
            <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <span class="font-semibold">{{ $singleClass->name }}</span>
            <span class="text-gray-400">
                ({{ $singleClass->grade->name }}) · {{ $singleClass->academicYear->name }}
            </span>
            <i class="ti ti-lock absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 text-sm" 
               title="You are assigned to this class only"></i>
        </div>
    @else
        {{-- Multiple classes: show dropdown --}}
        <div class="relative">
            <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <select name="class_id" x-model="classId" required
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm
                           focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                <option value="">— Select Class —</option>
                @foreach ($classes as $cls)
                    <option value="{{ $cls->id }}">
                        {{ $cls->name }} ({{ $cls->grade->name }}) · {{ $cls->academicYear->name }}
                    </option>
                @endforeach
            </select>
        </div>
    @endif
</div>

                {{-- Period Type --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Period <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="period" value="monthly" x-model="period" class="peer sr-only">
                            <div class="text-center py-2.5 rounded-xl border-2 border-gray-200 
                                        peer-checked:border-green-500 peer-checked:bg-green-50 
                                        peer-checked:text-green-700 text-sm font-semibold transition-all">
                                Monthly
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="period" value="semester" x-model="period" class="peer sr-only">
                            <div class="text-center py-2.5 rounded-xl border-2 border-gray-200 
                                        peer-checked:border-green-500 peer-checked:bg-green-50 
                                        peer-checked:text-green-700 text-sm font-semibold transition-all">
                                Semester
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="period" value="annual" x-model="period" class="peer sr-only">
                            <div class="text-center py-2.5 rounded-xl border-2 border-gray-200 
                                        peer-checked:border-green-500 peer-checked:bg-green-50 
                                        peer-checked:text-green-700 text-sm font-semibold transition-all">
                                Annual
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Month Selector --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div x-show="period === 'monthly'">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Month <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-calendar-month absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <select name="month" x-model="month"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm
                                       focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                            @foreach (\App\Helpers\AcademicCalendar::monthDropdown() as $n => $name)
    <option value="{{ $n }}">Month {{ $n }} — {{ $name }}</option>
@endforeach
                        </select>
                    </div>
                </div>

                <div x-show="period === 'semester'">
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                        Semester <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-calendar-stats absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        <select name="semester" x-model="semester"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm
                                       focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                           <option value="1">{{ \App\Helpers\AcademicCalendar::semesterLabel(1) }}</option>
<option value="2">{{ \App\Helpers\AcademicCalendar::semesterLabel(2) }}</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-400 flex items-center gap-1.5">
                    <i class="ti ti-info-circle text-base"></i>
                    Report will open in a new tab
                </p>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 
                               text-white text-sm font-semibold rounded-xl transition-all shadow-sm 
                               hover:shadow-green-500/20 active:scale-[0.98]">
                    <i class="ti ti-printer text-base"></i>
                    Generate Report
                </button>
            </div>
        </form>
    </div>

</div>

@endsection