@extends('layouts.admin', ['title' => 'Student Attendance'])

@push('navbar-actions')
<form method="GET"
      action="{{ route('teacher.student-attendance.index') }}"
      id="filter-form"
      class="flex items-center gap-2">

    {{-- Unified filter pill --}}
    <div class="flex items-center divide-x divide-gray-200 bg-gray-100 border border-gray-200 rounded-lg overflow-hidden">

        <div class="relative">
            <i class="ti ti-building absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            <select name="class_id" id="sel-class"
                    class="bg-transparent border-none pl-7 pr-5 py-1.5 text-sm font-medium text-gray-700
                           focus:outline-none focus:ring-0 cursor-pointer appearance-none">
                <option value="">— Class —</option>
                @foreach ($classes as $cls)
                    <option value="{{ $cls->id }}"
                        {{ isset($class) && $cls->id === $class->id ? 'selected' : '' }}>
                        {{ $cls->name }} ({{ $cls->grade->name }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="relative">
            <i class="ti ti-calendar absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            <select name="month" id="sel-month"
                    class="bg-transparent border-none pl-7 pr-5 py-1.5 text-sm font-medium text-gray-700
                           focus:outline-none focus:ring-0 cursor-pointer appearance-none">
                <option value="">— Month —</option>
                @foreach ([1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'] as $n => $name)
                    <option value="{{ $n }}"
                        {{ isset($month) && $n === $month ? 'selected'
                            : ($n == now()->month && !isset($month) ? 'selected' : '') }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="relative">
            <select name="year" id="sel-year"
                    class="bg-transparent border-none pl-3 pr-5 py-1.5 text-sm font-medium text-gray-700
                           focus:outline-none focus:ring-0 cursor-pointer appearance-none">
                <option value="">— Year —</option>
                @for ($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}"
                        {{ isset($year) && $y === $year ? 'selected'
                            : ($y == now()->year && !isset($year) ? 'selected' : '') }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>
        </div>

    </div>
</form>
@endpush

@section('content')

@php
    $hasSheet     = isset($class);
    $today        = Carbon\Carbon::today();
    $todayStr     = $today->format('Y-m-d');
    $todayInMonth = $hasSheet && $today->month === $month && $today->year === $year;
@endphp

{{-- ── Empty state ─────────────────────────────────────────── --}}
@if (! $hasSheet)
    <div class="flex flex-col items-center justify-center min-h-[62vh] text-center select-none">
        <div class="w-16 h-16 rounded-2xl bg-green-50 flex items-center justify-center mb-4">
            <i class="ti ti-calendar-check text-green-400 text-3xl"></i>
        </div>
        <p class="text-gray-600 font-semibold">No class selected</p>
        <p class="text-gray-400 text-sm mt-1 max-w-xs">
            Pick a class, month, and year from the top bar — the sheet loads automatically.
        </p>
    </div>

@elseif ($enrollments->isEmpty())
    <div class="flex flex-col items-center justify-center min-h-[62vh] text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mb-4">
            <i class="ti ti-users-off text-gray-300 text-3xl"></i>
        </div>
        <p class="text-gray-500 font-semibold">No students found</p>
        <p class="text-gray-400 text-sm mt-1">This class has no active enrollments.</p>
    </div>

@else

{{-- ── Sheet header ─────────────────────────────────────────── --}}
<div class="mb-4 flex items-center justify-between flex-wrap gap-3">
    <div>
        <h2 class="text-base font-bold text-gray-800">
            {{ $class->name }}
            <span class="text-gray-400 font-normal">· {{ $class->grade->name }}</span>
        </h2>
        <p class="text-xs text-gray-400 mt-0.5">
            {{ Carbon\Carbon::create($year, $month)->format('F Y') }}
            · {{ $enrollments->count() }} students
        </p>
    </div>

    {{-- Live badge counters --}}
    <div class="flex items-center gap-2 flex-wrap">
        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">
            <i class="ti ti-circle-check"></i> <span id="badge-p">0</span>
        </span>
        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">
            <i class="ti ti-circle-x"></i> <span id="badge-a">0</span>
        </span>
        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700 text-xs font-semibold">
            <i class="ti ti-clock"></i> <span id="badge-l">0</span>
        </span>
        <span class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold">
            <i class="ti ti-notes"></i> <span id="badge-e">0</span>
        </span>
    </div>
</div>

{{-- ── Quick mark today ─────────────────────────────────────── --}}
@if ($todayInMonth)
<div class="mb-4 flex items-center justify-between bg-white border border-gray-200 rounded-xl px-4 py-3 flex-wrap gap-3 shadow-sm">
    <div class="flex items-center gap-2 text-sm text-gray-600">
        <i class="ti ti-bolt text-yellow-500 text-base"></i>
        <span>Quick mark <strong>today</strong> ({{ $today->format('d M') }}) for all students:</span>
    </div>
    <div class="flex items-center gap-2">
        <button type="button" onclick="markAllToday('present')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                       bg-green-100 text-green-700 hover:bg-green-200 border border-green-200 transition-colors">
            <i class="ti ti-circle-check"></i> All Present
        </button>
        <button type="button" onclick="markAllToday('absent')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                       bg-red-100 text-red-700 hover:bg-red-200 border border-red-200 transition-colors">
            <i class="ti ti-circle-x"></i> All Absent
        </button>
        <button type="button" onclick="markAllToday('late')"
                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold
                       bg-yellow-100 text-yellow-700 hover:bg-yellow-200 border border-yellow-200 transition-colors">
            <i class="ti ti-clock"></i> All Late
        </button>
    </div>
</div>
@endif

{{-- ── Attendance table ─────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-x-auto shadow-sm">
    <table class="text-xs border-collapse" id="att-table">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
                <th class="px-2 py-3 text-center text-gray-400 font-semibold border-r border-gray-200 sticky left-0 bg-gray-50 z-10 w-8">#</th>
                <th class="px-3 py-3 text-left text-gray-500 font-semibold border-r border-gray-200 sticky left-8 bg-gray-50 z-10 min-w-44">Name</th>
                @foreach ($dates as $date)
                    @php
                        $isToday  = $date->isSameDay($today);
                        $isWknd   = $date->isWeekend();
                        $isFuture = $date->gt($today);
                    @endphp
                    <th class="px-1 py-2 text-center font-semibold border-r border-gray-100 w-10
                        {{ $isToday  ? 'bg-blue-50 text-blue-600 ring-1 ring-inset ring-blue-200' : '' }}
                        {{ $isWknd  && !$isToday ? 'bg-gray-100 text-gray-300' : '' }}
                        {{ $isFuture && !$isToday ? 'text-gray-300' : '' }}
                        {{ !$isWknd && !$isFuture && !$isToday ? 'text-gray-500' : '' }}">
                        <div>{{ $date->format('d') }}</div>
                        <div class="font-normal text-gray-400" style="font-size:9px">{{ $date->format('D') }}</div>
                    </th>
                @endforeach
                <th class="px-2 py-3 text-center font-semibold text-green-600 border-l border-gray-200 w-8 bg-green-50">P</th>
                <th class="px-2 py-3 text-center font-semibold text-red-600 w-8 bg-red-50">A</th>
                <th class="px-2 py-3 text-center font-semibold text-yellow-600 w-8 bg-yellow-50">L</th>
                <th class="px-2 py-3 text-center font-semibold text-blue-600 w-8 bg-blue-50">E</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @php
                $avatarColors = [
                    'bg-blue-100 text-blue-700',
                    'bg-purple-100 text-purple-700',
                    'bg-orange-100 text-orange-700',
                    'bg-teal-100 text-teal-700',
                    'bg-pink-100 text-pink-700',
                    'bg-indigo-100 text-indigo-700',
                ];
            @endphp
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
                    $avatarColor = $avatarColors[$rowIndex % count($avatarColors)];
                    $initials    = strtoupper(substr($enrollment->student->full_name, 0, 2));
                @endphp
                <tr class="hover:bg-gray-50 transition-colors" data-enrollment="{{ $enrollment->id }}">
                    <td class="px-2 py-2 text-center text-gray-400 border-r border-gray-200 sticky left-0 bg-white z-10">
                        {{ $rowIndex + 1 }}
                    </td>
                    <td class="px-3 py-2 font-medium text-gray-800 border-r border-gray-200 sticky left-8 bg-white z-10 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center flex-shrink-0 text-xs font-bold {{ $avatarColor }}">
                                {{ $initials }}
                            </div>
                            {{ $enrollment->student->full_name }}
                        </div>
                    </td>

                    @foreach ($dates as $date)
                        @php
                            $dateStr   = $date->format('Y-m-d');
                            $status    = $attendanceMap[$enrollment->id][$dateStr] ?? '';
                            $isWknd    = $date->isWeekend();
                            $isFuture  = $date->gt($today);
                            $isToday   = $date->isSameDay($today);
                            $disabled  = $isWknd || $isFuture;
                            $label     = match($status) {
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
                        <td class="px-0.5 py-1 text-center border-r border-gray-100
                            {{ $disabled ? 'bg-gray-50' : ($isToday ? 'bg-blue-50/40' : '') }}"
                            data-date="{{ $dateStr }}">
                            @if ($disabled)
                                <span class="text-gray-200">—</span>
                            @else
                                <button type="button"
                                        data-date="{{ $dateStr }}"
                                        data-enrollment="{{ $enrollment->id }}"
                                        data-student="{{ $enrollment->student->full_name }}"
                                        data-status="{{ $status }}"
                                        onclick="openModal(this)"
                                        class="attendance-btn w-8 h-7 rounded-lg border font-bold
                                               transition-all cursor-pointer hover:scale-110 hover:shadow-sm
                                               {{ $cellColor }}">
                                    {{ $label ?: '·' }}
                                </button>
                            @endif
                        </td>
                    @endforeach

                    <td class="px-2 py-2 text-center font-bold text-green-700 bg-green-50 border-l border-gray-200">
                        <span class="row-p">{{ $p ?: '—' }}</span>
                    </td>
                    <td class="px-2 py-2 text-center font-bold text-red-700 bg-red-50">
                        <span class="row-a">{{ $a ?: '—' }}</span>
                    </td>
                    <td class="px-2 py-2 text-center font-bold text-yellow-700 bg-yellow-50">
                        <span class="row-l">{{ $l ?: '—' }}</span>
                    </td>
                    <td class="px-2 py-2 text-center font-bold text-blue-700 bg-blue-50">
                        <span class="row-e">{{ $e ?: '—' }}</span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Legend --}}
<div class="mt-3 flex items-center gap-4 text-xs text-gray-400 flex-wrap">
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-green-100 inline-block"></span> Present</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-red-100 inline-block"></span> Absent</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-yellow-100 inline-block"></span> Late</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-100 inline-block"></span> Excused</span>
    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded bg-blue-50 border border-blue-200 inline-block"></span> Today</span>
    <span>— weekend &nbsp;· faded = future</span>
</div>

@endif

{{-- ── Modal ────────────────────────────────────────────────── --}}
<div id="att-modal"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 hidden">
    <div class="bg-white rounded-2xl shadow-xl w-80 p-5">
        <div class="flex items-center gap-3 mb-4">
            <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-calendar-check text-green-600"></i>
            </div>
            <div>
                <p class="text-xs text-gray-400" id="modal-date"></p>
                <p class="text-sm font-semibold text-gray-800" id="modal-student"></p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 mb-4">
            <button type="button" onclick="selectStatus('present')" id="btn-present"
                    class="status-btn flex items-center justify-center gap-1.5 py-3 rounded-lg border-2 font-semibold text-sm transition-all border-green-300 bg-green-50 text-green-700 hover:bg-green-100">
                <i class="ti ti-circle-check"></i> Present
            </button>
            <button type="button" onclick="selectStatus('absent')" id="btn-absent"
                    class="status-btn flex items-center justify-center gap-1.5 py-3 rounded-lg border-2 font-semibold text-sm transition-all border-red-300 bg-red-50 text-red-700 hover:bg-red-100">
                <i class="ti ti-circle-x"></i> Absent
            </button>
            <button type="button" onclick="selectStatus('late')" id="btn-late"
                    class="status-btn flex items-center justify-center gap-1.5 py-3 rounded-lg border-2 font-semibold text-sm transition-all border-yellow-300 bg-yellow-50 text-yellow-700 hover:bg-yellow-100">
                <i class="ti ti-clock"></i> Late
            </button>
            <button type="button" onclick="selectStatus('excused')" id="btn-excused"
                    class="status-btn flex items-center justify-center gap-1.5 py-3 rounded-lg border-2 font-semibold text-sm transition-all border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100">
                <i class="ti ti-notes"></i> Excused
            </button>
        </div>
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1">Note (optional)</label>
            <textarea id="modal-note" rows="2" placeholder="e.g. sick, family event..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                             focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
        </div>
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
                    class="flex-1 py-2 text-sm bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700">
                Confirm
            </button>
        </div>
    </div>
</div>

{{-- ── Toast (auto-save feedback) ──────────────────────────── --}}
<div id="att-toast"
     class="fixed bottom-6 right-6 z-50 hidden items-center gap-3
            bg-gray-900 text-white text-sm rounded-xl px-4 py-3 shadow-xl min-w-[200px]">
    <i class="ti ti-circle-check text-green-400 text-base flex-shrink-0" id="toast-icon"></i>
    <span id="toast-msg" class="flex-1">Saved</span>
    <button onclick="undoLast()"
            id="toast-undo"
            class="text-xs font-semibold text-yellow-400 hover:text-yellow-300 flex-shrink-0">
        Undo
    </button>
    <div id="toast-bar"
         class="absolute bottom-0 left-0 h-0.5 bg-green-400 rounded-full"
         style="width:100%"></div>
</div>

@push('scripts')
<script>
const SAVE_URL = "{{ route('teacher.student-attendance.save-single') }}";
const CSRF     = "{{ csrf_token() }}";
const CLASS_ID = {{ $class->id ?? 'null' }};
const TODAY    = "{{ $todayStr ?? '' }}";

// ── State ──────────────────────────────────────────────────────
let currentBtn     = null;
let selectedStatus = '';
let undoStack      = null;
let toastTimer     = null;

// ── Color map ──────────────────────────────────────────────────
const colorMap = {
    present: { cls: 'bg-green-100 text-green-700 border-green-300',   label: 'P' },
    absent:  { cls: 'bg-red-100 text-red-700 border-red-300',         label: 'A' },
    late:    { cls: 'bg-yellow-100 text-yellow-700 border-yellow-300', label: 'L' },
    excused: { cls: 'bg-blue-100 text-blue-700 border-blue-300',       label: 'E' },
    '':      { cls: 'bg-white text-gray-300 border-gray-200',          label: '·' },
};
const allCls = [...new Set(
    Object.values(colorMap).flatMap(v => v.cls.split(' '))
)];

// ── Modal ──────────────────────────────────────────────────────
function openModal(btn) {
    currentBtn     = btn;
    selectedStatus = btn.dataset.status || '';
    document.getElementById('modal-date').textContent    = btn.dataset.date;
    document.getElementById('modal-student').textContent = btn.dataset.student;
    document.getElementById('modal-note').value          = '';
    highlightStatusBtn(selectedStatus);
    document.getElementById('att-modal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('att-modal').classList.add('hidden');
    currentBtn = null; selectedStatus = '';
}
function selectStatus(s) { selectedStatus = s; highlightStatusBtn(s); }
function clearStatus()   { selectedStatus = ''; highlightStatusBtn(''); }

function highlightStatusBtn(s) {
    ['btn-present','btn-absent','btn-late','btn-excused'].forEach(id =>
        document.getElementById(id).classList.remove('ring-4','ring-offset-1','scale-105')
    );
    const map = { present:'btn-present', absent:'btn-absent', late:'btn-late', excused:'btn-excused' };
    if (map[s]) document.getElementById(map[s]).classList.add('ring-4','ring-offset-1','scale-105');
}

function confirmModal() {
    if (!currentBtn) return;
    const prev = currentBtn.dataset.status || '';
    const note = document.getElementById('modal-note').value.trim();
    applyStatus(currentBtn, selectedStatus);
    saveCell(currentBtn.dataset.date, currentBtn.dataset.enrollment, selectedStatus, note, prev);
    closeModal();
}

// ── Apply status to cell button ────────────────────────────────
function applyStatus(btn, status) {
    const c = colorMap[status] || colorMap[''];
    btn.classList.remove(...allCls);
    btn.classList.add(...c.cls.split(' '));
    btn.textContent    = c.label;
    btn.dataset.status = status;
    updateRowTotals(btn.closest('tr'));
    updateBadges();
}

// ── AJAX save ─────────────────────────────────────────────────
async function saveCell(date, enrollmentId, status, note, prevStatus) {
    try {
        const res = await fetch(SAVE_URL, {
            method:  'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body:    JSON.stringify({ class_id: CLASS_ID, date, enrollment_id: enrollmentId, status, note }),
        });
        const data = await res.json();
        if (data.success) {
            showToast('Saved', date, enrollmentId, prevStatus, true);
        } else {
            showToast('Failed to save', null, null, null, false);
        }
    } catch {
        showToast('Failed to save', null, null, null, false);
    }
}

// ── Undo ──────────────────────────────────────────────────────
async function undoLast() {
    if (!undoStack) return;
    const { date, enrollmentId, prevStatus } = undoStack;
    const btn = document.querySelector(
        `.attendance-btn[data-date="${date}"][data-enrollment="${enrollmentId}"]`
    );
    if (btn) applyStatus(btn, prevStatus);
    await saveCell(date, enrollmentId, prevStatus, '', null);
    hideToast();
}

// ── Toast ─────────────────────────────────────────────────────
function showToast(msg, date, enrollmentId, prevStatus, success) {
    if (success && date) undoStack = { date, enrollmentId, prevStatus };
    else undoStack = null;

    clearTimeout(toastTimer);
    const toast = document.getElementById('att-toast');
    const bar   = document.getElementById('toast-bar');
    const icon  = document.getElementById('toast-icon');
    const undo  = document.getElementById('toast-undo');

    document.getElementById('toast-msg').textContent = msg;
    icon.className = success
        ? 'ti ti-circle-check text-green-400 text-base flex-shrink-0'
        : 'ti ti-alert-circle text-red-400 text-base flex-shrink-0';
    undo.classList.toggle('hidden', !success || !prevStatus);

    toast.classList.remove('hidden');
    toast.classList.add('flex');

    // Progress bar
    bar.style.transition = 'none';
    bar.style.width      = '100%';
    requestAnimationFrame(() => requestAnimationFrame(() => {
        bar.style.transition = 'width 5s linear';
        bar.style.width      = '0%';
    }));

    toastTimer = setTimeout(hideToast, 5000);
}

function hideToast() {
    const toast = document.getElementById('att-toast');
    toast.classList.add('hidden');
    toast.classList.remove('flex');
    undoStack = null;
}

// ── Quick mark today ──────────────────────────────────────────
function markAllToday(status) {
    if (!TODAY) return;
    document.querySelectorAll(`.attendance-btn[data-date="${TODAY}"]`).forEach(btn => {
        const prev = btn.dataset.status || '';
        if (prev === status) return; // skip if already same
        applyStatus(btn, status);
        saveCell(TODAY, btn.dataset.enrollment, status, '', prev);
    });
}

// ── Row totals ────────────────────────────────────────────────
function updateRowTotals(row) {
    if (!row) return;
    let p = 0, a = 0, l = 0, e = 0;
    row.querySelectorAll('.attendance-btn').forEach(b => {
        const s = b.dataset.status;
        if (s === 'present') p++;
        else if (s === 'absent') a++;
        else if (s === 'late') l++;
        else if (s === 'excused') e++;
    });
    row.querySelector('.row-p').textContent = p || '—';
    row.querySelector('.row-a').textContent = a || '—';
    row.querySelector('.row-l').textContent = l || '—';
    row.querySelector('.row-e').textContent = e || '—';
}

// ── Global badge counters ────────────────────────────────────
function updateBadges() {
    let p = 0, a = 0, l = 0, e = 0;
    document.querySelectorAll('.attendance-btn').forEach(b => {
        const s = b.dataset.status;
        if (s === 'present') p++;
        else if (s === 'absent') a++;
        else if (s === 'late') l++;
        else if (s === 'excused') e++;
    });
    document.getElementById('badge-p').textContent = p;
    document.getElementById('badge-a').textContent = a;
    document.getElementById('badge-l').textContent = l;
    document.getElementById('badge-e').textContent = e;
}

// ── Init ──────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    // Filter auto-submit
    const form = document.getElementById('filter-form');
    const sc   = document.getElementById('sel-class');
    const sm   = document.getElementById('sel-month');
    const sy   = document.getElementById('sel-year');
    if (form && sc && sm && sy) {
        [sc, sm, sy].forEach(el => el.addEventListener('change', () => {
            if (sc.value && sm.value && sy.value) form.submit();
        }));
    }

    // Seed badge counters from server-rendered data
    updateBadges();

    // Close modal on backdrop click
    document.getElementById('att-modal')?.addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
});
</script>
@endpush

@endsection