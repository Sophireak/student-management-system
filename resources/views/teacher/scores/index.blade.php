@extends('layouts.teacher', ['title' => 'Scores'])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $hasFilter   = isset($class) && isset($selectedPeriod);
@endphp

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <p class="text-sm text-gray-500">
        @if ($hasFilter)
            {{ $class->name }} · {{ $class->grade->name }} — {{ $periodLabel }}
        @else
            Select a class and period to begin.
        @endif
    </p>

    @if ($hasFilter)
        <a href="{{ route($routePrefix . '.scores.report', request()->query()) }}"
           class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
            <i class="ti ti-file-text text-base"></i> View Official Report
        </a>
    @endif
</div>


{{-- Filter Bar --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET"
          action="{{ route($routePrefix . '.scores.index') }}"
          id="filter-form"
          class="flex flex-wrap items-end gap-3">

        {{-- Class --}}
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Class</label>
            <div class="relative">
                <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <select name="class_id" id="sel-class" required
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">— Select Class —</option>
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}" {{ ($class->id ?? null) === $cls->id ? 'selected' : '' }}>
                            {{ $cls->name }} ({{ $cls->grade->name }}) · {{ $cls->academicYear->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Period --}}
        <div class="flex-1 min-w-48">
            <label class="block text-xs font-medium text-gray-500 mb-1">Period</label>
            <div class="relative">
                <i class="ti ti-calendar-stats absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <select name="period" id="sel-period" required
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">— Select Period —</option>
                    <optgroup label="Monthly">
                        @foreach ([1=>'September',2=>'October',3=>'November',4=>'December',5=>'January',6=>'February',7=>'March',8=>'April',9=>'May'] as $num => $name)
                            <option value="month_{{ $num }}" {{ ($selectedPeriod ?? '') === 'month_'.$num ? 'selected' : '' }}>
                                Month {{ $num }} — {{ $name }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Semester">
                        <option value="semester_1" {{ ($selectedPeriod ?? '') === 'semester_1' ? 'selected' : '' }}>Semester 1 (Sep – Jan)</option>
                        <option value="semester_2" {{ ($selectedPeriod ?? '') === 'semester_2' ? 'selected' : '' }}>Semester 2 (Feb – May)</option>
                    </optgroup>
                </select>
            </div>
        </div>
    </form>
</div>

{{-- Empty State (no filter yet) --}}
@if (! $hasFilter)
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
        <i class="ti ti-clipboard-list text-5xl text-gray-300 block mb-3"></i>
        <p class="text-gray-500 text-sm">Select a class and period above to view subjects.</p>
    </div>

{{-- No subjects --}}
@elseif ($subjects->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
        <i class="ti ti-book-off text-4xl text-gray-300 block mb-2"></i>
        <p class="text-gray-400 text-sm">No subjects configured for {{ $class->grade->name }}.</p>
    </div>

{{-- Dashboard --}}
@else

    {{-- Progress Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Students</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalStudents }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Subjects</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $subjects->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Overall Progress</p>
            <div class="flex items-center gap-3 mt-1">
                <p class="text-2xl font-bold text-gray-800">{{ $overallProgress }}%</p>
                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full transition-all"
                         style="width: {{ $overallProgress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lock Status --}}
    <div class="flex items-center justify-between bg-white rounded-xl border border-gray-200 px-5 py-3 mb-5">
        <div class="flex items-center gap-2 text-sm">
            @if ($isLocked)
                <i class="ti ti-lock text-red-500 text-base"></i>
                <span class="text-red-700 font-medium">Locked</span>
                <span class="text-gray-400">— Scores cannot be edited</span>
            @else
                <i class="ti ti-lock-open text-green-500 text-base"></i>
                <span class="text-green-700 font-medium">Unlocked</span>
                <span class="text-gray-400">— Scores can be edited</span>
            @endif
        </div>

        @if (auth()->user()->isAdmin())
            <form method="POST" action="{{ route('admin.scores.' . ($isLocked ? 'unlock' : 'lock')) }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">
                <input type="hidden" name="period"   value="{{ $selectedPeriod }}">
                <button type="submit"
                        class="text-xs font-medium px-3 py-1.5 rounded-lg transition-colors
                               {{ $isLocked ? 'bg-yellow-50 border border-yellow-200 text-yellow-700 hover:bg-yellow-100'
                                            : 'bg-gray-50 border border-gray-200 text-gray-600 hover:bg-red-50 hover:border-red-200 hover:text-red-700' }}">
                    {{ $isLocked ? 'Unlock Sheet' : 'Lock Sheet' }}
                </button>
            </form>
        @endif
    </div>

    {{-- Subject Cards --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-gray-700">Subjects</h2>
            <p class="text-xs text-gray-400">Click a subject to enter scores</p>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach ($subjects as $subject)
                @php
                    $completed = $completionMap[$subject->id]['completed'] ?? 0;
                    $percent   = $totalStudents > 0 ? round(($completed / $totalStudents) * 100) : 0;
                    $isDone    = $percent >= 100;
                    $isEmpty   = $completed === 0;
                @endphp

                <a href="{{ route($routePrefix . '.scores.input', array_merge(request()->query(), ['subject_id' => $subject->id])) }}"
                   class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 transition-colors group">

                    {{-- Icon --}}
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $isDone ? 'bg-green-100 text-green-600' : ($isEmpty ? 'bg-gray-100 text-gray-400' : 'bg-yellow-100 text-yellow-600') }}">
                        <i class="ti {{ $isDone ? 'ti-check' : 'ti-pencil' }} text-lg"></i>
                    </div>

                    {{-- Subject Info --}}
<div class="flex-1 min-w-0">
    <p class="text-sm font-semibold text-gray-800 truncate">{{ $subject->name }}</p>
    <p class="text-xs text-gray-400 mt-0.5">
        @if ($subject->isNumeric()) Numeric · Max: 10
        @elseif ($subject->isGrade()) Grade-based
        @else Pass / Fail
        @endif
    </p>
</div>

                    {{-- Progress --}}
                    <div class="hidden sm:flex flex-col items-end gap-1 w-40">
                        <p class="text-xs font-medium text-gray-600">{{ $completed }} / {{ $totalStudents }}</p>
                        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all
                                        {{ $isDone ? 'bg-green-500' : ($isEmpty ? 'bg-gray-300' : 'bg-yellow-500') }}"
                                 style="width: {{ $percent }}%"></div>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <span class="hidden md:inline-flex text-xs font-medium px-2 py-1 rounded-md
                                 {{ $isDone ? 'bg-green-50 text-green-700' : ($isEmpty ? 'bg-gray-50 text-gray-500' : 'bg-yellow-50 text-yellow-700') }}">
                        {{ $isDone ? 'Complete' : ($isEmpty ? 'Not Started' : 'In Progress') }}
                    </span>

                    <i class="ti ti-chevron-right text-gray-300 group-hover:text-gray-500 transition-colors"></i>
                </a>
            @endforeach
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form      = document.getElementById('filter-form');
    const selClass  = document.getElementById('sel-class');
    const selPeriod = document.getElementById('sel-period');

    function trySubmit() {
        if (selClass.value && selPeriod.value) form.submit();
    }
    selClass.addEventListener('change', trySubmit);
    selPeriod.addEventListener('change', trySubmit);
});
</script>
@endpush

@endsection