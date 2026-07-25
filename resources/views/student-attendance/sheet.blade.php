@extends('layouts.admin', ['title' => 'Attendance — ' . $class->name])

@section('content')

@php
    $isAdmin     = auth()->user()->isAdmin();
    $routePrefix = $isAdmin ? 'admin' : 'teacher';
    $monthName   = Carbon\Carbon::create($year, $month)->format('F');
    $today       = Carbon\Carbon::today();
@endphp

{{-- Header --}}
<div class="mb-5">
    <a href="{{ route($routePrefix . '.student-attendance.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-3">
        <i class="ti ti-arrow-left text-base"></i> Student Attendance
    </a>
    <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-calendar-check text-green-600 text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold text-gray-800">{{ $class->name }} <span class="text-gray-400 font-normal">· {{ $class->grade->name }}</span></h1>
            <p class="text-sm text-gray-500">{{ $monthName }} {{ $year }}</p>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif

{{-- Filter bar --}}
<div class="mb-4 bg-white {{ $isAdmin ? 'rounded-xl' : 'rounded-2xl shadow-sm' }} border border-gray-200 px-4 py-3">
    <form method="GET"
          action="{{ route($routePrefix . '.student-attendance.sheet') }}"
          id="filter-form"
          class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Class</label>
            <div class="relative">
                <i class="ti ti-building absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <select name="class_id" id="sel-class"
                        class="border border-gray-300 rounded-lg pl-8 pr-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 hover:border-green-300 transition-colors">
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $cls->id === $class->id ? 'selected' : '' }}>
                            {{ $cls->name }} ({{ $cls->grade->name }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Month</label>
            <div class="relative">
                <i class="ti ti-calendar absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <select name="month" id="sel-month"
                        class="border border-gray-300 rounded-lg pl-8 pr-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 hover:border-green-300 transition-colors">
                    @foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                        <option value="{{ $n }}" {{ $n === $month ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Year</label>
            <div class="relative">
                <i class="ti ti-calendar-stats absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <select name="year" id="sel-year"
                        class="border border-gray-300 rounded-lg pl-8 pr-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 hover:border-green-300 transition-colors">
                    @for ($y = now()->year; $y >= now()->year - 2; $y--)
                        <option value="{{ $y }}" {{ $y === $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </form>
</div>

{{-- Legend --}}
<div class="mb-4 flex flex-wrap items-center gap-2 text-xs">
    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 text-green-700 font-semibold">
        <i class="ti ti-circle-check text-sm"></i> Present
    </span>
    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 font-semibold">
        <i class="ti ti-circle-x text-sm"></i> Absent
    </span>
    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 font-semibold">
        <i class="ti ti-clock text-sm"></i> Late
    </span>
    <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold">
        <i class="ti ti-notes text-sm"></i> Excused
    </span>
    <span class="text-gray-400 flex items-center gap-1 ml-1">
        <i class="ti ti-hand-click text-sm"></i> Click a cell to mark attendance
    </span>
</div>

@if ($enrollments->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-12 text-center">
        <i class="ti ti-users-off text-4xl text-gray-300 block mb-2"></i>
        <p class="text-gray-400 text-sm">No active students in this class.</p>
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

        <div class="bg-white rounded-xl border border-gray-200 overflow-x-auto">
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
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-2 py-2 text-center text-gray-400 border-r border-gray-200 sticky left-0 bg-white z-10">{{ $rowIndex + 1 }}</td>
                            <td class="px-3 py-2 font-medium text-gray-800 border-r border-gray-200 sticky left-8 bg-white z-10 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                        <i class="ti ti-user text-green-600" style="font-size: 11px;"></i>
                                    </div>
                                    {{ $enrollment->student->full_name }}
                                </div>
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
                                                class="attendance-btn w-8 h-7 rounded-lg border font-bold text-xs
                                                       transition-colors cursor-pointer hover:scale-105 {{ $cellColor }}">
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

        <div class="mt-4 flex items-center justify-between bg-white rounded-xl border border-gray-200 px-5 py-4">
            <p class="text-xs text-gray-400 flex items-center gap-1.5">
                <i class="ti ti-info-circle text-sm"></i> Click a cell to mark. Weekends and future dates are disabled.
            </p>
            <button type="submit"
                    class="flex items-center gap-2 px-6 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                <i class="ti ti-device-floppy text-base"></i> Save Attendance
            </button>
        </div>
    </form>
@endif

{{-- Modal --}}
<div id="att-modal"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-80 p-5">

        {{-- Modal header --}}
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-calendar-check text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400" id="modal-date"></p>
                <p class="text-sm font-semibold text-gray-800" id="modal-student"></p>
            </div>
        </div>

        {{-- Status buttons --}}
        <div class="grid grid-cols-2 gap-2 mb-4">
            <button type="button" onclick="selectStatus('present')"
                    id="btn-present"
                    class="status-btn flex items-center justify-center gap-1.5 py-3 rounded-lg border-2 font-semibold text-sm transition-all
                           border-green-300 bg-green-50 text-green-700 hover:bg-green-100">
                <i class="ti ti-circle-check text-base"></i> Present
            </button>
            <button type="button" onclick="selectStatus('absent')"
                    id="btn-absent"
                    class="status-btn flex items-center justify-center gap-1.5 py-3 rounded-lg border-2 font-semibold text-sm transition-all
                           border-red-300 bg-red-50 text-red-700 hover:bg-red-100">
                <i class="ti ti-circle-x text-base"></i> Absent
            </button>
            <button type="button" onclick="selectStatus('late')"
                    id="btn-late"
                    class="status-btn flex items-center justify-center gap-1.5 py-3 rounded-lg border-2 font-semibold text-sm transition-all
                           border-yellow-300 bg-yellow-50 text-yellow-700 hover:bg-yellow-100">
                <i class="ti ti-clock text-base"></i> Late
            </button>
            <button type="button" onclick="selectStatus('excused')"
                    id="btn-excused"
                    class="status-btn flex items-center justify-center gap-1.5 py-3 rounded-lg border-2 font-semibold text-sm transition-all
                           border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100">
                <i class="ti ti-notes text-base"></i> Excused
            </button>
        </div>

        {{-- Reason --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">Reason / Note (optional)</label>
            <textarea id="modal-note" rows="2"
                      placeholder="e.g. sick, family event..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                             focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-none"></textarea>
        </div>

        {{-- Actions --}}
        <div class="flex gap-2">
            <button type="button" onclick="clearStatus()"
                    class="flex-1 py-2 text-sm border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 transition-colors">
                Clear
            </button>
            <button type="button" onclick="closeModal()"
                    class="flex-1 py-2 text-sm border border-gray-300 rounded-lg text-gray-500 hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="button" onclick="confirmModal()"
                    class="flex-1 py-2 text-sm bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors">
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
