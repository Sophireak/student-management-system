@extends('layouts.admin', ['title' => 'Student Attendance'])

@section('content')

@php $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher'; @endphp

<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-700">Student Attendance</h2>
    <p class="text-sm text-gray-400 mt-0.5">Select a class and month to view or fill the attendance sheet.</p>
</div>

@if ($classes->isEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-8 text-center text-gray-400 text-sm">
        No classes available.
    </div>
@else
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 max-w-xl">
        <form method="GET"
              action="{{ route($routePrefix . '.student-attendance.sheet') }}"
              id="filter-form">

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

            <div class="mb-4 flex gap-3">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select name="month" id="sel-month" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">— Month —</option>
                        @foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                            <option value="{{ $n }}" {{ $n == now()->month ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-28">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" id="sel-year" required
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-400">
                        @for ($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <p class="text-xs text-gray-400">Sheet loads automatically when all fields are selected.</p>

        </form>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form     = document.getElementById('filter-form');
    const selClass = document.getElementById('sel-class');
    const selMonth = document.getElementById('sel-month');
    const selYear  = document.getElementById('sel-year');

    function trySubmit() {
        if (selClass.value && selMonth.value && selYear.value) form.submit();
    }

    selClass.addEventListener('change', trySubmit);
    selMonth.addEventListener('change', trySubmit);
    selYear.addEventListener('change', trySubmit);
});
</script>
@endpush

@endsection