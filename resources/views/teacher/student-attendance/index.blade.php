@extends('layouts.teacher', ['title' => 'វត្តមានសិស្ស'])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
@endphp

<div x-data="attendancePage()" x-init="init()">

    {{-- Header + Date Picker --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-check text-green-600 text-lg"></i>
            </div>
            <h1 class="text-xl font-extrabold text-gray-800 tracking-tight">វត្តមានសិស្ស</h1>
        </div>

        {{-- Date Picker --}}
        <form method="GET" action="{{ route($routePrefix . '.student-attendance.index') }}" class="flex items-center gap-2">
            @if($class)
                <input type="hidden" name="class_id" value="{{ $class->id }}">
            @endif
            <input type="hidden" name="period" value="{{ $period }}">
            <div class="relative">
                <input type="date" name="date" value="{{ $date }}"
       max="{{ now()->format('Y-m-d') }}"
       onchange="handleDateChange(this)"
       class="rounded-xl border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold
              focus:border-green-500 focus:ring-2 focus:ring-green-100
              cursor-pointer min-w-[180px]">
            </div>
        </form>
    </div>
{{-- Filters Row (only show if multiple classes) --}}
@if($classes->count() > 1)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-4">
        <form method="GET" action="{{ route($routePrefix . '.student-attendance.index') }}">
            <input type="hidden" name="date" value="{{ $date }}">

            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <i class="ti ti-building text-gray-400"></i>
                </div>
                <select name="class_id" onchange="this.form.submit()"
                        class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm 
                               border-gray-200 bg-gray-50 focus:bg-white 
                               focus:border-green-500 focus:ring-2 focus:ring-green-100
                               appearance-none cursor-pointer">
                    @foreach ($classes as $cls)
                        <option value="{{ $cls->id }}" {{ $class && $cls->id == $class->id ? 'selected' : '' }}>
                            {{ $cls->name }} · {{ $cls->grade->name }} · 
                            {{ $cls->session_period === 'morning' ? 'ព្រឹក' : 'រសៀល' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
@else
    {{-- Show class info as read-only banner if only 1 class --}}
    @if($class)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 mb-4 
                    flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-100 to-emerald-100 
                        text-green-700 flex items-center justify-center font-bold shadow-inner">
                {{ strtoupper(substr($class->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-bold text-gray-800">
                    {{ $class->name }} · {{ $class->grade->name }}
                </p>
                <p class="text-xs text-gray-400">
                    {{ $class->session_period === 'morning' ? 'ព្រឹក (7:00 - 11:00)' : 'រសៀល (13:00 - 17:00)' }}
                </p>
            </div>
        </div>
    @endif
@endif

    {{-- Empty State: No Class Selected --}}
    @if(!$class)
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center 
                        justify-center mx-auto mb-4 border border-gray-100">
                <i class="ti ti-select text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-800 mb-1">Please select a class</h3>
            <p class="text-sm text-gray-500">
                Choose a class from the dropdown above to start marking attendance.
            </p>
        </div>
        @elseif($isFuture)
    {{-- Future Date --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 flex items-center 
                    justify-center mx-auto mb-4 border border-amber-100">
            <i class="ti ti-calendar-off text-2xl text-amber-400"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-800 mb-1">Future Date</h3>
        <p class="text-sm text-gray-500">Cannot mark attendance for future dates.</p>
    </div>
    @elseif($isSunday)
        {{-- Sunday --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-red-50 flex items-center 
                        justify-center mx-auto mb-4 border border-red-100">
                <i class="ti ti-calendar-off text-2xl text-red-400"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-800 mb-1">ថ្ងៃអាទិត្យ</h3>
            <p class="text-sm text-gray-500">Sunday is a rest day. No attendance needed.</p>
        </div>
    @elseif($enrollments->isEmpty())
        {{-- No Students --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-16 text-center">
            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center 
                        justify-center mx-auto mb-4 border border-gray-100">
                <i class="ti ti-users-off text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-800 mb-1">No students enrolled</h3>
            <p class="text-sm text-gray-500 mb-4">This class has no active enrollments.</p>
            <a href="{{ route('admin.enrollments.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 
                      rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                <i class="ti ti-user-plus"></i> Enroll Students
            </a>
        </div>
    @else
            {{-- Stats Bar --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-4">
            <div class="grid grid-cols-4 gap-4">
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-400 mb-1">សរុប:</p>
                    <p class="text-2xl font-extrabold text-blue-600">{{ $total }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-400 mb-1">វត្តមាន:</p>
                    <p class="text-2xl font-extrabold text-green-600" x-text="stats.present">{{ $presentCount }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-400 mb-1">អវត្តមាន:</p>
                    <p class="text-2xl font-extrabold text-red-600" x-text="stats.absent">{{ $absentCount }}</p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-medium text-gray-400 mb-1">យឺត:</p>
                    <p class="text-2xl font-extrabold text-amber-600" x-text="stats.late">{{ $lateCount }}</p>
                </div>
            </div>
        </div>

        {{-- Locked Warning --}}
        @if($isLocked)
            <div class="bg-gray-100 border border-gray-200 rounded-2xl px-4 py-3 mb-4 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-lock text-gray-500 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-700">Attendance is locked</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        @if(auth()->user()->isAdmin())
                            You can still edit as admin.
                        @else
                            Session has passed. Contact admin to make changes.
                        @endif
                    </p>
                </div>
            </div>
        @endif
        @if($isPast && auth()->user()->isAdmin())
    <div class="bg-blue-50 border border-blue-100 rounded-2xl px-4 py-3 mb-4 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-info-circle text-blue-600 text-sm"></i>
        </div>
        <div class="flex-1">
            <p class="text-sm font-bold text-blue-700">Past date — Admin edit mode</p>
            <p class="text-xs text-blue-500 mt-0.5">
                You are editing a past date. Changes will be saved.
            </p>
        </div>
    </div>
@endif

        {{-- Student Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pb-56 sm:pb-48">
            @foreach ($enrollments as $i => $enrollment)
                @php
                    $status = $attendanceMap[$enrollment->id]['status'] ?? 'present';
                    $notes = $attendanceMap[$enrollment->id]['notes'] ?? '';
                    $initials = mb_substr($enrollment->student->last_name ?? '', 0, 1) 
                              . mb_substr($enrollment->student->first_name ?? '', 0, 1);
                @endphp
                <div class="bg-white rounded-2xl border shadow-sm p-4 transition-all"
                     :class="isLocked ? 'border-gray-200 opacity-70' : getCardBorder('{{ $enrollment->id }}')">

                    <div class="flex items-center gap-3">
                        {{-- Number --}}
                        <span class="text-sm font-bold text-gray-400 min-w-[24px] text-center">
                            {{ $i + 1 }}
                        </span>

                        {{-- Avatar with Khmer initials --}}
                        <div class="w-11 h-11 rounded-full flex items-center justify-center 
                                    font-bold text-sm shadow-inner flex-shrink-0
                                    {{ $isLocked ? 'bg-gray-300 text-gray-500' : 'bg-slate-800 text-white' }}">
                            {{ $initials }}
                        </div>

                        {{-- Name + ID --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">
                                {{ $enrollment->student->full_name }}
                            </p>
                            <p class="text-[11px] text-gray-400 font-mono truncate">
                                {{ $enrollment->student->student_id }}
                            </p>
                        </div>

                        {{-- Status Buttons --}}
                        <div class="flex items-center gap-1 flex-shrink-0">
                            {{-- Present --}}
                            <button 
                                @click="setStatus('{{ $enrollment->id }}', 'present')"
                                :disabled="isLocked"
                                :class="getBtnClass('{{ $enrollment->id }}', 'present')"
                                class="w-8 h-8 rounded-lg flex items-center justify-center 
                                       transition-all active:scale-90 disabled:cursor-not-allowed">
                                <i class="ti ti-check text-base"></i>
                            </button>

                            {{-- Absent --}}
                            <button 
                                @click="setStatus('{{ $enrollment->id }}', 'absent')"
                                :disabled="isLocked"
                                :class="getBtnClass('{{ $enrollment->id }}', 'absent')"
                                class="w-8 h-8 rounded-lg flex items-center justify-center 
                                       transition-all active:scale-90 disabled:cursor-not-allowed">
                                <i class="ti ti-x text-base"></i>
                            </button>

                            {{-- Late --}}
                            <button 
                                @click="setStatus('{{ $enrollment->id }}', 'late')"
                                :disabled="isLocked"
                                :class="getBtnClass('{{ $enrollment->id }}', 'late')"
                                class="w-8 h-8 rounded-lg flex items-center justify-center 
                                       transition-all active:scale-90 disabled:cursor-not-allowed">
                                <i class="ti ti-clock text-base"></i>
                            </button>

                            {{-- Excused --}}
<button 
    @click="setStatus('{{ $enrollment->id }}', 'excused')"
    :disabled="isLocked"
    :class="getBtnClass('{{ $enrollment->id }}', 'excused')"
    class="w-8 h-8 rounded-lg flex items-center justify-center 
           transition-all active:scale-90 disabled:cursor-not-allowed">
    <i class="ti ti-calendar text-base"></i>
</button>
                        </div>
                    </div>

                    {{-- Inline Reason Input (shows when Excused is selected) --}}
<template x-if="getStatus('{{ $enrollment->id }}') === 'excused'">
    <div class="mt-3 pt-3 border-t border-gray-100">
        <input 
            type="text"
            :value="getNotes('{{ $enrollment->id }}')"
            @blur="updateNotes('{{ $enrollment->id }}', $event.target.value)"
            @keydown.enter="$event.target.blur()"
            :disabled="isLocked"
            placeholder="មូលហេតុ..."
            class="w-full px-3 py-2 text-sm rounded-lg 
                   border border-gray-200 bg-gray-50 
                   focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100
                   transition-all disabled:bg-gray-100 disabled:cursor-not-allowed"
        />
    </div>
</template>
                </div>
            @endforeach
        </div>

{{-- Bottom Save Bar --}}
<div class="fixed bottom-24 left-3 right-3 z-30 
            bg-white/85 backdrop-blur-xl border border-white/80 
            rounded-2xl shadow-lg shadow-green-900/10">
    <div class="max-w-3xl mx-auto px-4 py-3">

        {{-- Mobile: Compact --}}
        <div class="flex items-center justify-between gap-2 sm:hidden">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-bold text-gray-800 truncate">
                    {{ $khmerDate }}
                </p>
                <div class="flex items-center gap-2 mt-0.5">
                    <template x-if="saveStatus === 'saved'">
                        <span class="text-[10px] text-green-600 font-medium">
                            <i class="ti ti-check text-xs"></i> រក្សាទុករួច
                        </span>
                    </template>
                    <template x-if="hasChanges && !saveStatus">
                        <span class="text-[10px] text-amber-600 font-medium">
                            <i class="ti ti-alert-circle text-xs"></i> មានការផ្លាស់ប្តូរ
                        </span>
                    </template>
                    <template x-if="!hasChanges && !saveStatus">
                        <span class="text-[10px] text-gray-400">No changes</span>
                    </template>
                </div>
            </div>

            @if($isLocked)
                <button disabled 
                        class="px-4 py-2 bg-gray-200 text-gray-400 text-xs font-bold 
                               rounded-xl cursor-not-allowed whitespace-nowrap">
                    មិនអាចកែប្រែ
                </button>
            @else
                <button @click="saveAttendance()"
                        :disabled="isSaving"
                        class="px-4 py-2 text-white text-xs font-bold rounded-xl 
                               transition-all shadow-sm active:scale-[0.98]
                               disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                        :class="hasChanges 
                            ? 'bg-green-600 hover:bg-green-700' 
                            : 'bg-slate-800 hover:bg-slate-900'">
                    <span x-show="!isSaving">
                        <template x-if="hasChanges">
                            <span>រក្សាទុក</span>
                        </template>
                        <template x-if="!hasChanges">
                            <span>រក្សាទុករួច ✓</span>
                        </template>
                    </span>
                    <span x-show="isSaving">
                        <i class="ti ti-loader animate-spin text-xs"></i> ...
                    </span>
                </button>
            @endif
        </div>

        {{-- Desktop/Tablet --}}
        <div class="hidden sm:flex items-center justify-between gap-3">
            <div>
                <p class="text-sm font-bold text-gray-800">រក្សាទុកវត្តមាន</p>
                <p class="text-xs text-gray-500">{{ $khmerDate }}</p>
            </div>

            <div class="flex items-center gap-2">
                <template x-if="saveStatus === 'saved'">
                    <span class="flex items-center gap-1.5 text-xs text-green-600 font-medium">
                        <i class="ti ti-check text-sm"></i> រក្សាទុករួចរាល់
                    </span>
                </template>
                <template x-if="hasChanges && !saveStatus">
                    <span class="flex items-center gap-1.5 text-xs text-amber-600 font-medium">
                        <i class="ti ti-alert-circle text-sm"></i> មានការផ្លាស់ប្តូរ
                    </span>
                </template>

                @if($isLocked)
                    <button disabled 
                            class="px-6 py-2.5 bg-gray-200 text-gray-400 text-sm font-bold 
                                   rounded-xl cursor-not-allowed">
                        មិនអាចកែប្រែបាន
                    </button>
                @else
                    <button @click="saveAttendance()"
                            :disabled="isSaving"
                            class="px-6 py-2.5 text-white text-sm font-bold rounded-xl 
                                   transition-all shadow-sm active:scale-[0.98]
                                   disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="hasChanges 
                                ? 'bg-green-600 hover:bg-green-700 hover:shadow-green-500/20' 
                                : 'bg-slate-800 hover:bg-slate-900 hover:shadow-slate-500/20'">
                        <span x-show="!isSaving">
                            <template x-if="hasChanges">
                                <span><i class="ti ti-device-floppy text-base mr-1"></i>រក្សាទុក</span>
                            </template>
                            <template x-if="!hasChanges">
                                <span><i class="ti ti-check text-base mr-1"></i>រក្សាទុករួច</span>
                            </template>
                        </span>
                        <span x-show="isSaving">
                            <i class="ti ti-loader animate-spin text-base mr-1"></i>Saving...
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@endif
</div>
@push('scripts')
<script>
    function handleDateChange(input) {
        const selected = new Date(input.value + 'T00:00:00');
        const dayOfWeek = selected.getDay();

        if (dayOfWeek === 0) {
            alert('ថ្ងៃអាទិត្យ - Sunday is a rest day. Please select another date.');
            input.value = '{{ $date }}';
            return;
        }

        input.form.submit();
    }

    function attendancePage() {
        return {
            classId: {{ $class?->id ?? 'null' }},
            date: '{{ $date }}',
            period: '{{ $period }}',
            isLocked: @json($isLocked),
            isSaving: false,
            hasChanges: false,
            saveStatus: '',

            data: {},
            originalData: {},

            stats: {
                present: {{ $presentCount }},
                absent: {{ $absentCount }},
                late: {{ $lateCount }},
                excused: {{ $excusedCount }},
            },

            init() {
                // Load initial data
                @foreach ($enrollments as $enrollment)
                    this.data['{{ $enrollment->id }}'] = {
                        status: '{{ $attendanceMap[$enrollment->id]['status'] ?? 'present' }}',
                        notes: {!! json_encode($attendanceMap[$enrollment->id]['notes'] ?? '') !!}
                    };
                @endforeach

                // Snapshot for dirty checking
                this.originalData = JSON.parse(JSON.stringify(this.data));

                // Warn before leaving with unsaved changes
                window.addEventListener('beforeunload', (e) => {
                    if (this.hasChanges) {
                        e.preventDefault();
                        e.returnValue = 'You have unsaved attendance. Leave anyway?';
                        return e.returnValue;
                    }
                });
            },

            // ── Getters ──
            getStatus(enrollmentId) {
                return this.data[enrollmentId]?.status || '';
            },

            getNotes(enrollmentId) {
                return this.data[enrollmentId]?.notes || '';
            },

            getBtnClass(enrollmentId, status) {
                if (this.isLocked) {
                    return this.getStatus(enrollmentId) === status
                        ? 'bg-gray-300 text-gray-500'
                        : 'bg-gray-100 text-gray-300';
                }

                const active = this.getStatus(enrollmentId) === status;

                const colors = {
                    present: active 
                        ? 'bg-green-500 text-white shadow-sm' 
                        : 'bg-gray-100 text-gray-400 hover:bg-green-100 hover:text-green-600',
                    absent: active 
                        ? 'bg-red-500 text-white shadow-sm' 
                        : 'bg-gray-100 text-gray-400 hover:bg-red-100 hover:text-red-600',
                    late: active 
                        ? 'bg-amber-500 text-white shadow-sm' 
                        : 'bg-gray-100 text-gray-400 hover:bg-amber-100 hover:text-amber-600',
                    excused: active 
                        ? 'bg-blue-500 text-white shadow-sm' 
                        : 'bg-gray-100 text-gray-400 hover:bg-blue-100 hover:text-blue-600',
                };

                return colors[status] || '';
            },

            getCardBorder(enrollmentId) {
                const status = this.getStatus(enrollmentId);
                const borders = {
                    present: 'border-green-100',
                    absent: 'border-red-100',
                    late: 'border-amber-100',
                    excused: 'border-blue-100',
                };
                return borders[status] || 'border-gray-200';
            },

            // ── Actions ──
            setStatus(enrollmentId, status) {
                if (this.isLocked) return;

                this.data[enrollmentId] = {
                    status,
                    notes: status === 'excused' 
                        ? (this.data[enrollmentId]?.notes || '') 
                        : ''
                };

                this.recalcStats();
                this.checkChanges();
            },

            updateNotes(enrollmentId, notes) {
                if (this.isLocked) return;
                if (this.data[enrollmentId]?.status !== 'excused') return;
                if (this.data[enrollmentId]?.notes === notes) return;

                this.data[enrollmentId].notes = notes;
                this.checkChanges();
            },

            checkChanges() {
                this.hasChanges = JSON.stringify(this.data) !== JSON.stringify(this.originalData);
            },

            // ── Save All ──
            async saveAttendance() {
                if (this.isLocked || this.isSaving) return;

                this.isSaving = true;
                this.saveStatus = '';

                // Build payload
                const attendance = Object.keys(this.data).map(enrollmentId => ({
                    enrollment_id: parseInt(enrollmentId),
                    status: this.data[enrollmentId].status,
                    notes: this.data[enrollmentId].notes || '',
                }));

                try {
                    const response = await axios.post(
                        '{{ route($routePrefix . ".student-attendance.save") }}',
                        {
                            class_id: this.classId,
                            date: this.date,
                            period: this.period,
                            attendance: attendance,
                        }
                    );

                    if (response.data.success) {
                        this.originalData = JSON.parse(JSON.stringify(this.data));
                        this.hasChanges = false;
                        this.saveStatus = 'saved';
                        setTimeout(() => this.saveStatus = '', 3000);
                    }
                } catch (error) {
                    console.error(error);
                    alert('Error saving attendance. Please try again.');
                } finally {
                    this.isSaving = false;
                }
            },

            recalcStats() {
                let present = 0, absent = 0, late = 0, excused = 0;
                for (const id in this.data) {
                    const s = this.data[id]?.status;
                    if (s === 'present') present++;
                    if (s === 'absent') absent++;
                    if (s === 'late') late++;
                    if (s === 'excused') excused++;
                }
                this.stats = { present, absent, late, excused };
            },
        };
    }
</script>
@endpush

@endsection
