@extends('layouts.admin', ['title' => 'New Attendance Session'])

@section('content')

<div class="max-w-md">
    <a href="{{ route('teacher.attendance-sessions.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Sessions
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">
            Create Attendance Session
        </h2>

        @if ($classes->isEmpty())
            <p class="text-sm text-gray-400">
                You have no classes assigned for the active academic year.
            </p>
        @else
            <form method="POST"
                  action="{{ route('teacher.attendance-sessions.store') }}"
                  novalidate>
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Class <span class="text-red-500">*</span>
                    </label>
                    <select name="class_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500
                                   @error('class_id') border-red-400 @enderror">
                        <option value="">— Select Class —</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}"
                                {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} — {{ $class->grade->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <select name="subject_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500
                                   @error('subject_id') border-red-400 @enderror">
                        <option value="">— Select Subject —</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                                ({{ $subject->grade->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date"
                           name="session_date"
                           value="{{ old('session_date', now()->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('session_date') border-red-400 @enderror">
                    @error('session_date')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Period
                    </label>
                    <select name="period"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— None —</option>
                        <option value="morning"
                            {{ old('period') === 'morning' ? 'selected' : '' }}>
                            Morning
                        </option>
                        <option value="afternoon"
                            {{ old('period') === 'afternoon' ? 'selected' : '' }}>
                            Afternoon
                        </option>
                    </select>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Topic
                        <span class="text-gray-400 text-xs font-normal">(optional)</span>
                    </label>
                    <input type="text"
                           name="topic"
                           value="{{ old('topic') }}"
                           placeholder="e.g. Chapter 3 — Fractions"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit"
                        class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                               rounded-md hover:bg-blue-700">
                    Create Session
                </button>
            </form>
        @endif
    </div>
</div>

@endsection