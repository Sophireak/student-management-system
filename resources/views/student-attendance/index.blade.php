@extends('layouts.admin', ['title' => 'Student Attendance'])

@section('content')

@php $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher'; @endphp

{{-- Page Header --}}
<div class="mb-6 flex items-center gap-4">
    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
        <i class="ti ti-calendar-check text-green-600 text-xl"></i>
    </div>
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Student Attendance</h1>
        <p class="text-sm text-gray-500 mt-1">Select a class and month to view or fill the attendance sheet.</p>
    </div>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">

        {{-- Card header strip --}}
        <div class="bg-green-50 border-b border-green-100 px-6 py-3 flex items-center gap-2">
            <i class="ti ti-filter text-green-600 text-base"></i>
            <h2 class="text-xs font-semibold text-green-700 uppercase tracking-wider">Select Filter</h2>
        </div>

        <div class="p-6">
            <form method="GET" action="{{ route($routePrefix . '.student-attendance.sheet') }}" id="filter-form">

                {{-- Class --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none"></i>
                        <select name="class_id" id="sel-class" required
                                class="w-full appearance-none border border-gray-300 rounded-lg pl-9 pr-9 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                       hover:border-green-300 transition-colors bg-white">
                            <option value="">— Select Class —</option>
                            @foreach ($classes as $cls)
                                <option value="{{ $cls->id }}">
                                    {{ $cls->name }} ({{ $cls->grade->name }}) · {{ $cls->academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                        <i class="ti ti-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    </div>
                    @if ($classes->isEmpty())
                        <p class="mt-2 text-xs text-yellow-600 flex items-center gap-1">
                            <i class="ti ti-alert-triangle"></i> No active classes available.
                        </p>
                    @endif
                </div>

                {{-- Month --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Month <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none"></i>
                        <select name="month" id="sel-month" required
                                class="w-full appearance-none border border-gray-300 rounded-lg pl-9 pr-9 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                       hover:border-green-300 transition-colors bg-white">
                            <option value="">— Select Month —</option>
                            @foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                                <option value="{{ $n }}" {{ $n == now()->month ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <i class="ti ti-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    </div>
                </div>

                {{-- Year --}}
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Year <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-calendar-stats absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base pointer-events-none"></i>
                        <select name="year" id="sel-year" required
                                class="w-full appearance-none border border-gray-300 rounded-lg pl-9 pr-9 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                       hover:border-green-300 transition-colors bg-white">
                            <option value="">— Select Year —</option>
                            @for ($y = now()->year; $y >= now()->year - 2; $y--)
                                <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <i class="ti ti-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                    </div>
                </div>

                {{-- Hint --}}
                <div class="mt-5 flex items-center gap-2 text-xs text-gray-400">
                    <i class="ti ti-info-circle text-sm"></i>
                    The sheet opens automatically once all three fields are selected.
                </div>

            </form>
        </div>
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
