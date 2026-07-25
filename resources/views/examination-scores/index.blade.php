@extends('layouts.admin', ['title' => 'Examination Scores'])

@section('content')

@php
    $isAdmin     = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'admin' : 'teacher';
    $cardRadius  = $isAdmin ? 'rounded-xl' : 'rounded-2xl';
    $cardShadow  = $isAdmin ? '' : 'shadow-sm';
    $selRadius   = $isAdmin ? 'rounded-lg' : 'rounded-full';
    $btnRadius   = $isAdmin ? 'rounded-lg' : 'rounded-full';
@endphp

{{-- Page Header --}}
@if ($isAdmin)
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Examination Scores</h1>
        <p class="text-sm text-gray-500 mt-1">Select a class and period to load scores.</p>
    </div>
</div>
@else
<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 shadow-sm">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
            <i class="ti ti-clipboard-list text-green-600 text-xl"></i>
        </div>
        <div>
            <h1 class="text-lg font-bold text-gray-800 leading-tight">Examination Scores</h1>
            <span class="inline-flex items-center gap-1 mt-0.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
                <i class="ti ti-filter text-sm"></i> Select a class and period
            </span>
        </div>
    </div>
</div>
@endif

{{-- Alert --}}
@if (session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif

{{-- Filter Card --}}
<div class="max-w-2xl">
    <div class="bg-white {{ $cardRadius }} border border-gray-200 p-6 {{ $cardShadow }}">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Select Filter</h2>

        <form method="GET"
              action="{{ route($routePrefix . '.examination-scores.sheet') }}"
              id="filter-form">

            {{-- Class --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <select name="class_id" id="sel-class" required
                            class="w-full border border-gray-300 {{ $selRadius }} pl-9 pr-3 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">— Select Class —</option>
                        @foreach ($classes as $cls)
                            <option value="{{ $cls->id }}">
                                {{ $cls->name }} ({{ $cls->grade->name }}) · {{ $cls->academicYear->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Period --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Period <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="ti ti-calendar-stats absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <select name="period" id="sel-period" required
                            class="w-full border border-gray-300 {{ $selRadius }} pl-9 pr-3 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">— Select Period —</option>
                        <optgroup label="Monthly">
                            @foreach ([1=>'September',2=>'October',3=>'November',4=>'December',5=>'January',6=>'February',7=>'March',8=>'April',9=>'May'] as $num => $name)
                                <option value="month_{{ $num }}">Month {{ $num }} — {{ $name }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Semester">
                            <option value="semester_1">Semester 1 (Sep – Jan)</option>
                            <option value="semester_2">Semester 2 (Feb – May)</option>
                        </optgroup>
                    </select>
                </div>
            </div>

            <p class="text-xs text-gray-400 mb-4">Sheet loads automatically when all fields are selected.</p>

            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold {{ $btnRadius }} transition-colors">
                <i class="ti ti-eye text-base"></i> View Score Sheet
            </button>

        </form>
    </div>
</div>

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
