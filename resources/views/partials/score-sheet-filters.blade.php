@props(['classes', 'examSessions', 'selectedClassId', 'routePrefix'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
    <h3 class="text-sm font-semibold text-gray-700 mb-4">Select Score Sheet</h3>

    {{-- Step 1: Pick a class --}}
    <form method="GET"
          action="{{ route($routePrefix . '.score-sheet.index') }}"
          class="mb-4">
        <div class="flex items-end gap-3 flex-wrap">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Class
                </label>
                <select name="class_id"
                        onchange="this.form.submit()"
                        class="w-full border border-gray-300 rounded-md
                               px-3 py-2 text-sm focus:outline-none
                               focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select Class —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}"
                            {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                            — {{ $class->grade->name }}
                            ({{ $class->academicYear->name }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    {{-- Step 2: Pick an exam session --}}
    @if ($selectedClassId && $examSessions->isNotEmpty())
        <form method="GET"
              action="{{ route($routePrefix . '.score-sheet.load') }}">
            <div class="flex items-end gap-3 flex-wrap">
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Exam Session
                    </label>
                    <select name="exam_session_id"
                            class="w-full border border-gray-300 rounded-md
                                   px-3 py-2 text-sm focus:outline-none
                                   focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select Exam Session —</option>
                        @foreach ($examSessions as $session)
                            <option value="{{ $session->id }}">
                                {{ $session->full_label }}
                                — {{ $session->subject->name }}
                                (Max: {{ $session->max_score }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white text-sm
                               font-medium rounded-md hover:bg-blue-700">
                    Load Sheet
                </button>
            </div>
        </form>
    @elseif ($selectedClassId && $examSessions->isEmpty())
        <p class="text-sm text-yellow-600 bg-yellow-50 border border-yellow-200
                  rounded-md px-4 py-2">
            No exam sessions found for this class.
            @if ($routePrefix === 'admin')
                <a href="{{ route('admin.exam-sessions.create') }}"
                   class="underline ml-1">Create one</a>.
            @endif
        </p>
    @endif
</div>