@extends('layouts.admin', ['title' => 'Attendance — ' . $class->name])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $monthName   = Carbon\Carbon::create($year, $month)->format('F');
    $today       = Carbon\Carbon::today();
@endphp

{{-- Header --}}
<div class="mb-4">
    <a href="{{ route($routePrefix . '.student-attendance.index') }}"
       class="text-sm text-blue-600 hover:underline">← Student Attendance</a>
    <h2 class="text-lg font-semibold text-gray-700 mt-1">
        {{ $class->name }} · {{ $class->grade->name }}
    </h2>
    <p class="text-sm text-gray-400">{{ $monthName }} {{ $year }}</p>
</div>

@if (session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
        ✅ {{ session('success') }}
    </div>
@endif

{{-- Filter bar --}}
<div class="mb-4 bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3">
    <form method="GET"
          action="{{ route($routePrefix . '.student-attendance.sheet') }}"
          id="filter-form"
          class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Class</label>
            <select name="class_id" id="sel-class"
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                @foreach ($classes as $cls)
                    <option value="{{ $cls->id }}" {{ $cls->id === $class->id ? 'selected' : '' }}>
                        {{ $cls->name }} ({{ $cls->grade->name }})
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Month</label>
            <select name="month" id="sel-month"
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                @foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                    <option value="{{ $n }}" {{ $n === $month ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Year</label>
            <select name="year" id="sel-year"
                    class="border border-gray-300 rounded px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                @for ($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $y === $year ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </form>
</div>

{{-- Legend --}}
<div class="mb-3 flex flex-wrap items-center gap-3 text-xs">
    <span class="px-2 py-0.5 rounded bg-green-100 text-green-700 font-semibold">P Present</span>
    <span class="px-2 py-0.5 rounded bg-red-100 text-red-700 font-semibold">A Absent</span>
    <span class="px-2 py-0.5 rounded bg-yellow-100 text-yellow-700 font-semibold">L Late</span>
    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 font-semibold">E Excused</span>
    <span class="text-gray-400">Click a cell to mark attendance</span>
</div>

@if ($enrollments->isEmpty())
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-8 text-center text-gray-400 text-sm">
        No active students in this class.
    </div>
@else

    <form method="POST"
          action="{{ route($routePrefix . '.student-attendance.save') }}"
          id="attendance-form">
        @csrf
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <input type="hidden" name="month"    value="{{ $month }}">
        <input type="hidden" name="year"     value="{{ $year }}">

        {{-- Hidden inputs container — populated by JS --}}
        <div id="hidden-inputs"></div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-x-auto">
            <table class="text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-2 py-3 text-center font-semibold text-gray-500 border-r border-gray-200 sticky left-0 bg-gray-50 z-10 w-8">#</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-500 border-r border-gray-200 sticky left-8 bg-gray-50 z-10 min-w-40">Name</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-500 border-r border-gray-200 min-w-28">Phone</th>
                        @foreach ($dates as $date)
                            @php $isFuture = $date->gt($today); $isWknd = $date->isWeekend(); @endphp
                            <th class="px-1 py-2 text-center font-semibold border-r border-gray-100 w-10
                                {{ $isWknd ? 'bg-gray-100 text-gray-400' : ($isFuture ? 'bg-gray-50 text-gray-300' : 'text-gray-600') }}">
                                <div>{{ $date->format('d') }}</div>
                                <div class="font-normal text-gray-400 text-xs">{{ $date->format('D') }}</div>
                            </th>
                        @endforeach
                        <th class="px-2 py-3 text-center font-semibold text-green-600 border-l border-gray-200 w-8">P</th>
                        <th class="px-2 py-3 text-center font-semibold text-red-600 w-8">A</th>
                        <th class="px-2 py-3 text-center font-semibold text-yellow-600 w-8">L</th>
                        <th class="px-2 py-3 text-center font-semibold text-blue-600 w-8">E</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($enrollments as $rowIndex => $enrollment)
                        @php
                            $p = 0; $a = 0; $l = 0; $e = 0;
                            foreach ($dates as $date) {
                                $s = $attendanceMap[$enrollment->id][$date->format('Y-m-d')] ?? null;
                                if ($s === 'present') $p++;
                                elseif ($s === 'absent') $a++;
                                elseif ($s === 'late') $l++;
                                elseif ($s === 'excused') $e++;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 py-2 text-center text-gray-400 border-r border-gray-200 sticky left-0 bg-white z-10">{{ $rowIndex + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-gray-800 border-r border-gray-200 sticky left-8 bg-white z-10 whitespace-nowrap">
                                {{ $enrollment->student->full_name }}
                            </td>
                            <td class="px-3 py-2 text-gray-500 border-r border-gray-200 whitespace-nowrap">
                                {{ $enrollment->student->phone ?? '—' }}
                            </td>

                            @foreach ($dates as $date)
                                @php
                                    $dateStr  = $date->format('Y-m-d');
                                    $status   = $attendanceMap[$enrollment->id][$dateStr] ?? '';
                                    $isWknd   = $date->isWeekend();
                                    $isFuture = $date->gt($today);
                                    $disabled = $isWknd || $isFuture;

                                    $label = match($status) {
                                        'present' => 'P', 'absent' => 'A',
                                        'late'    => 'L', 'excused' => 'E',
                                        default   => ''
                                    };
                                    $cellColor = match($status) {
                                        'present' => 'bg-green-100 text-green-700 border-green-300',
                                        'absent'  => 'bg-red-100 text-red-700 border-red-300',
                                        'late'    => 'bg-yellow-100 text-yellow-700 border-yellow-300',
                                        'excused' => 'bg-blue-100 text-blue-700 border-blue-300',
                                        default   => 'bg-white text-gray-300 border-gray-200',
                                    };
                                @endphp
                                <td class="px-0.5 py-1 text-center border-r border-gray-100 {{ $disabled ? 'bg-gray-50' : '' }}">
                                    @if ($disabled)
                                        <span class="text-gray-200 text-xs">—</span>
                                    @else
                                        <button type="button"
                                                data-date="{{ $dateStr }}"
                                                data-enrollment="{{ $enrollment->id }}"
                                                data-student="{{ $enrollment->student->full_name }}"
                                                data-status="{{ $status }}"
                                                data-note="{{ $attendanceMap[$enrollment->id][$dateStr . '_note'] ?? '' }}"
                                                onclick="openModal(this)"
                                                class="attendance-btn w-8 h-7 rounded border font-bold text-xs
                                                       transition-colors cursor-pointer {{ $cellColor }}">
                                            {{ $label ?: '·' }}
                                        </button>
                                    @endif
                                </td>
                            @endforeach

                            <td class="px-2 py-2 text-center font-semibold text-green-600 border-l border-gray-200">{{ $p ?: '—' }}</td>
                            <td class="px-2 py-2 text-center font-semibold text-red-600">{{ $a ?: '—' }}</td>
                            <td class="px-2 py-2 text-center font-semibold text-yellow-600">{{ $l ?: '—' }}</td>
                            <td class="px-2 py-2 text-center font-semibold text-blue-600">{{ $e ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex items-center justify-between bg-white rounded-lg shadow-sm border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-400">Click a cell to mark. Weekends and future dates are disabled.</p>
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                Save Attendance
            </button>
        </div>
    </form>
@endif

{{-- Modal --}}
<div id="att-modal"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-xl shadow-xl w-80 p-5">

        {{-- Modal header --}}
        <div class="mb-4">
            <p class="text-xs text-gray-400" id="modal-date"></p>
            <p class="text-sm font-semibold text-gray-800 mt-0.5" id="modal-student"></p>
        </div>

        {{-- Status buttons --}}
        <div class="grid grid-cols-2 gap-2 mb-4">
            <button type="button" onclick="selectStatus('present')"
                    id="btn-present"
                    class="status-btn py-3 rounded-lg border-2 font-bold text-sm transition-all
                           border-green-300 bg-green-50 text-green-700 hover:bg-green-100">
                ✅ Present
            </button>
            <button type="button" onclick="selectStatus('absent')"
                    id="btn-absent"
                    class="status-btn py-3 rounded-lg border-2 font-bold text-sm transition-all
                           border-red-300 bg-red-50 text-red-700 hover:bg-red-100">
                ❌ Absent
            </button>
            <button type="button" onclick="selectStatus('late')"
                    id="btn-late"
                    class="status-btn py-3 rounded-lg border-2 font-bold text-sm transition-all
                           border-yellow-300 bg-yellow-50 text-yellow-700 hover:bg-yellow-100">
                🕐 Late
            </button>
            <button type="button" onclick="selectStatus('excused')"
                    id="btn-excused"
                    class="status-btn py-3 rounded-lg border-2 font-bold text-sm transition-all
                           border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100">
                📋 Excused
            </button>
        </div>

        {{-- Reason --}}
        <div class="mb-4">
            <label class="block text-xs text-gray-500 mb-1">Reason / Note (optional)</label>
            <textarea id="modal-note" rows="2"
                      placeholder="e.g. sick, family event..."
                      class="w-full border border-gray-300 rounded px-2 py-1.5 text-sm
                             focus:outline-none focus:ring-2 focus:ring-blue-400 resize-none"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2">
            <button type="button" onclick="clearStatus()"
                    class="flex-1 py-2 text-sm border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50">
                Clear
            </button>
            <button type="button" onclick="closeModal()"
                    class="flex-1 py-2 text-sm border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50">
                Cancel
            </button>
            <button type="button" onclick="confirmModal()"
                    class="flex-1 py-2 text-sm bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                Confirm
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
// State
let currentBtn     = null;
let selectedStatus = '';

const colorMap = {
    present: { btn: 'bg-green-100 text-green-700 border-green-300', label: 'P' },
    absent:  { btn: 'bg-red-100 text-red-700 border-red-300',       label: 'A' },
    late:    { btn: 'bg-yellow-100 text-yellow-700 border-yellow-300', label: 'L' },
    excused: { btn: 'bg-blue-100 text-blue-700 border-blue-300',    label: 'E' },
    '':      { btn: 'bg-white text-gray-300 border-gray-200',       label: '·' },
};

const allBtnClasses = [
    'bg-green-100','text-green-700','border-green-300',
    'bg-red-100','text-red-700','border-red-300',
    'bg-yellow-100','text-yellow-700','border-yellow-300',
    'bg-blue-100','text-blue-700','border-blue-300',
    'bg-white','text-gray-300','border-gray-200',
];

// Modal status button highlight
const statusBtnIds = {
    present: 'btn-present', absent: 'btn-absent',
    late: 'btn-late', excused: 'btn-excused'
};

function highlightStatusBtn(status) {
    Object.values(statusBtnIds).forEach(id => {
        document.getElementById(id).classList.remove('ring-4', 'ring-offset-1', 'scale-105');
    });
    if (status && statusBtnIds[status]) {
        const el = document.getElementById(statusBtnIds[status]);
        el.classList.add('ring-4', 'ring-offset-1', 'scale-105');
    }
}

function selectStatus(status) {
    selectedStatus = status;
    highlightStatusBtn(status);
}

function openModal(btn) {
    currentBtn     = btn;
    selectedStatus = btn.dataset.status || '';

    document.getElementById('modal-date').textContent    = btn.dataset.date;
    document.getElementById('modal-student').textContent = btn.dataset.student;
    document.getElementById('modal-note').value          = btn.dataset.note || '';

    highlightStatusBtn(selectedStatus);
    document.getElementById('att-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('att-modal').classList.add('hidden');
    currentBtn     = null;
    selectedStatus = '';
}

function clearStatus() {
    selectedStatus = '';
    highlightStatusBtn('');
    document.getElementById('modal-note').value = '';
}

function confirmModal() {
    if (!currentBtn) return;

    const date       = currentBtn.dataset.date;
    const enrollment = currentBtn.dataset.enrollment;
    const note       = document.getElementById('modal-note').value.trim();

    // Update button appearance
    const colors = colorMap[selectedStatus] || colorMap[''];
    currentBtn.classList.remove(...allBtnClasses);
    currentBtn.classList.add(...colors.btn.split(' '));
    currentBtn.textContent    = colors.label;
    currentBtn.dataset.status = selectedStatus;
    currentBtn.dataset.note   = note;

    // Update hidden inputs
    updateHiddenInputs(date, enrollment, selectedStatus, note);

    closeModal();
}

function updateHiddenInputs(date, enrollment, status, note) {
    const container = document.getElementById('hidden-inputs');

    // Remove existing inputs for this cell
    const existing = container.querySelectorAll(
        `[data-cell="${date}_${enrollment}"]`
    );
    existing.forEach(el => el.remove());

    if (!status) return; // empty = skip on save

    const statusInput = document.createElement('input');
    statusInput.type  = 'hidden';
    statusInput.name  = `attendance[${date}][${enrollment}]`;
    statusInput.value = status;
    statusInput.dataset.cell = `${date}_${enrollment}`;
    container.appendChild(statusInput);

    if (note) {
        const noteInput = document.createElement('input');
        noteInput.type  = 'hidden';
        noteInput.name  = `notes[${date}][${enrollment}]`;
        noteInput.value = note;
        noteInput.dataset.cell = `${date}_${enrollment}`;
        container.appendChild(noteInput);
    }
}

// Pre-populate hidden inputs from existing saved data
document.addEventListener('DOMContentLoaded', function () {
    // Auto-submit filter
    const form     = document.getElementById('filter-form');
    const selClass = document.getElementById('sel-class');
    const selMonth = document.getElementById('sel-month');
    const selYear  = document.getElementById('sel-year');

    [selClass, selMonth, selYear].forEach(el =>
        el.addEventListener('change', () => {
            if (selClass.value && selMonth.value && selYear.value) form.submit();
        })
    );

    // Seed hidden inputs from already-saved attendance on page load
    document.querySelectorAll('.attendance-btn[data-status]').forEach(btn => {
        const status = btn.dataset.status;
        const note   = btn.dataset.note || '';
        if (status) {
            updateHiddenInputs(btn.dataset.date, btn.dataset.enrollment, status, note);
        }
    });

    // Close modal on backdrop click
    document.getElementById('att-modal').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush

@endsection