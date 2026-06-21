@extends('layouts.admin', ['title' => 'Student Attendance'])

@section('content')

@php $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher'; @endphp

{{-- Page Header --}}
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Student Attendance</h1>
    <p class="text-sm text-gray-500 mt-1">Select a class and month to view or fill the attendance sheet.</p>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Select Filter</h2>

        <form method="GET" action="{{ route($routePrefix . '.student-attendance.sheet') }}"id="filter-form">

            {{-- Class --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <select name="class_id" id="sel-class" required
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
                @if ($classes->isEmpty())
                    <p class="mt-2 text-xs text-yellow-600 flex items-center gap-1">
                        <i class="ti ti-alert-triangle"></i> No active classes available.
                    </p>
                @endif
            </div>

            {{-- Month --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Month <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <select name="month" id="sel-month" required
                            class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">— Select Month —</option>
                        @foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                            <option value="{{ $n }}" {{ $n == now()->month ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Year --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Year <span class="text-red-500">*</span></label>
                <div class="relative">
                    <i class="ti ti-calendar-stats absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                    <select name="year" id="sel-year" required
                            class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">— Select Year —</option>
                         @for ($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            

        </form>
    </div>
</div>



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
