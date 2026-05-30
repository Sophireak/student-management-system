@extends('layouts.admin', ['title' => 'Mark Attendance'])

@section('content')

<div class="max-w-3xl">
    <a href="{{ route('teacher.attendance-sessions.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Sessions
    </a>

    {{-- Session info --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
        <h2 class="text-base font-bold text-gray-800">
            {{ $attendanceSession->schoolClass->name }}
            — {{ $attendanceSession->subject->name }}
        </h2>
        <p class="text-sm text-gray-500 mt-0.5">
            {{ $attendanceSession->session_date->format('l, M d, Y') }}
            @if ($attendanceSession->period)
                · {{ ucfirst($attendanceSession->period) }}
            @endif
        </p>
        @if ($attendanceSession->topic)
            <p class="text-xs text-gray-400 mt-1">
                Topic: {{ $attendanceSession->topic }}
            </p>
        @endif
    </div>

    {{-- Mark new attendance (unmarked students) --}}
    @if ($unmarkedEnrollments->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200
                    overflow-hidden mb-4">
            <div class="px-5 py-3 bg-yellow-50 border-b border-yellow-100">
                <h3 class="text-sm font-semibold text-yellow-800">
                    Mark Attendance
                    <span class="font-normal text-yellow-600">
                        ({{ $unmarkedEnrollments->count() }} students pending)
                    </span>
                </h3>
            </div>

            <form method="POST"
                  action="{{ route('teacher.attendance-sessions.attendance.store',
                                  $attendanceSession) }}"
                  novalidate>
                @csrf

                @foreach ($unmarkedEnrollments as $index => $enrollment)
                    <div class="px-5 py-3 border-b border-gray-100 last:border-0">
                        <input type="hidden"
                               name="attendance[{{ $index }}][enrollment_id]"
                               value="{{ $enrollment->id }}">

                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-medium text-gray-800 w-40 flex-shrink-0">
                                {{ $enrollment->student->full_name }}
                            </p>

                            {{-- Status buttons --}}
                            <div class="flex items-center gap-2 flex-wrap">
                                @foreach (['present', 'absent', 'late', 'excused'] as $status)
                                    <label class="cursor-pointer">
                                        <input type="radio"
                                               name="attendance[{{ $index }}][status]"
                                               value="{{ $status }}"
                                               class="sr-only peer"
                                               {{ $status === 'present' ? 'checked' : '' }}>
                                        <span class="px-3 py-1 text-xs rounded-full border
                                            border-gray-200 text-gray-500 capitalize
                                            peer-checked:font-semibold
                                            {{ match($status) {
                                                'present' => 'peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-200',
                                                'absent'  => 'peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-200',
                                                'late'    => 'peer-checked:bg-yellow-100 peer-checked:text-yellow-700 peer-checked:border-yellow-200',
                                                'excused' => 'peer-checked:bg-blue-100 peer-checked:text-blue-700 peer-checked:border-blue-200',
                                            } }}">
                                            {{ ucfirst($status) }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                            {{-- Notes --}}
                            <input type="text"
                                   name="attendance[{{ $index }}][notes]"
                                   placeholder="Note (optional)"
                                   class="text-xs border border-gray-200 rounded px-2 py-1
                                          w-36 focus:outline-none focus:ring-1
                                          focus:ring-blue-400 text-gray-600">
                        </div>
                    </div>
                @endforeach

                <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white text-sm font-medium
                                   rounded-md hover:bg-blue-700">
                        Save Attendance
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Already marked records --}}
    @if ($attendanceSession->attendances->isNotEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                <h3 class="text-sm font-semibold text-gray-700">
                    Marked Records
                    @if ($isFullyMarked)
                        <span class="ml-2 px-2 py-0.5 text-xs bg-green-100
                                     text-green-700 rounded-full">
                            Complete
                        </span>
                    @endif
                </h3>
            </div>

            @foreach ($attendanceSession->attendances as $record)
                <div class="flex items-center justify-between px-5 py-3
                            border-b border-gray-100 last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $record->enrollment->student->full_name }}
                        </p>
                        @if ($record->notes)
                            <p class="text-xs text-gray-400">{{ $record->notes }}</p>
                        @endif
                    </div>

                    {{-- Inline update form --}}
                    <form method="POST"
                          action="{{ route('teacher.attendance-sessions.attendance.update',
                                          [$attendanceSession, $record]) }}"
                          class="flex items-center gap-2">
                        @csrf
                        @method('PATCH')

                        <select name="status"
                                class="text-xs border border-gray-200 rounded px-2 py-1
                                       focus:outline-none focus:ring-1 focus:ring-blue-400
                                       {{ match($record->status) {
                                           'present' => 'text-green-700 bg-green-50',
                                           'absent'  => 'text-red-700 bg-red-50',
                                           'late'    => 'text-yellow-700 bg-yellow-50',
                                           'excused' => 'text-blue-700 bg-blue-50',
                                       } }}">
                            <option value="present"
                                {{ $record->status === 'present' ? 'selected' : '' }}>
                                Present
                            </option>
                            <option value="absent"
                                {{ $record->status === 'absent' ? 'selected' : '' }}>
                                Absent
                            </option>
                            <option value="late"
                                {{ $record->status === 'late' ? 'selected' : '' }}>
                                Late
                            </option>
                            <option value="excused"
                                {{ $record->status === 'excused' ? 'selected' : '' }}>
                                Excused
                            </option>
                        </select>

                        <input type="text"
                               name="notes"
                               value="{{ $record->notes }}"
                               placeholder="Note"
                               class="text-xs border border-gray-200 rounded px-2 py-1 w-28
                                      focus:outline-none focus:ring-1 focus:ring-blue-400">

                        <button type="submit"
                                class="text-xs px-2 py-1 bg-gray-100 text-gray-600
                                       rounded hover:bg-gray-200">
                            Update
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection