@extends('layouts.admin', ['title' => 'Examination Scores'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">Examination Scores</h2>
    <p class="text-sm text-gray-400 mt-0.5">Select a class and period to load scores.</p>
</div>

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
@endphp

@if (session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Single-row filter — auto-submits on change, no button --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3">
    <form method="GET"
          action="{{ route($routePrefix . '.examination-scores.sheet') }}"
          id="filter-form"
          class="flex flex-wrap items-end gap-3">

        {{-- Class --}}
        <div>
            <label class="block text-xs text-gray-500 mb-1">Class</label>
            <select name="class_id"
                    id="sel-class"
                    required
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400 min-w-40">
                <option value="">— Select Class —</option>
                @foreach ($classes as $cls)
                    <option value="{{ $cls->id }}">
                        {{ $cls->name }} ({{ $cls->grade->name }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Combined period: months + semesters in one dropdown --}}
        <div>
            <label class="block text-xs text-gray-500 mb-1">Period</label>
            <select name="period"
                    id="sel-period"
                    required
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm
                           focus:outline-none focus:ring-2 focus:ring-blue-400 min-w-48">
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

    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form      = document.getElementById('filter-form');
    const selClass  = document.getElementById('sel-class');
    const selPeriod = document.getElementById('sel-period');

    function trySubmit() {
        if (selClass.value && selPeriod.value) {
            form.submit();
        }
    }

    selClass.addEventListener('change', trySubmit);
    selPeriod.addEventListener('change', trySubmit);
});
</script>
@endpush

@endsection