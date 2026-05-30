@extends('layouts.admin', ['title' => 'New Exam Session'])

@section('content')

<div class="max-w-lg">
    <a href="{{ route('teacher.exam-sessions.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Exam Sessions
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">Create Exam Session</h2>

        @if ($classes->isEmpty())
            <p class="text-sm text-gray-400">
                You have no classes assigned for the active academic year.
            </p>
        @else
            <form method="POST"
                  action="{{ route('teacher.exam-sessions.store') }}"
                  novalidate>
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Class <span class="text-red-500">*</span>
                        </label>
                        <select name="class_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500
                                       @error('class_id') border-red-400 @enderror">
                            <option value="">— Select —</option>
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

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <select name="subject_id"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500
                                       @error('subject_id') border-red-400 @enderror">
                            <option value="">— Select —</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}"
                                    {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Exam Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="e.g. Midterm Exam"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select name="type"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500
                                       @error('type') border-red-400 @enderror">
                            <option value="">— Select —</option>
                            @foreach (['quiz' => 'Quiz', 'monthly' => 'Monthly', 'semester' => 'Semester', 'final' => 'Final'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('type') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                        <select name="term"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— None —</option>
                            @foreach (['term1' => 'Term 1', 'term2' => 'Term 2', 'term3' => 'Term 3'] as $val => $label)
                                <option value="{{ $val }}"
                                    {{ old('term') === $val ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Exam Date
                            <span class="text-gray-400 text-xs font-normal">(optional)</span>
                        </label>
                        <input type="date"
                               name="exam_date"
                               value="{{ old('exam_date') }}"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Max Score <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               name="max_score"
                               value="{{ old('max_score', 100) }}"
                               min="1"
                               max="1000"
                               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-blue-500
                                      @error('max_score') border-red-400 @enderror">
                        @error('max_score')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                               rounded-md hover:bg-blue-700">
                    Create Exam Session
                </button>
            </form>
        @endif
    </div>
</div>

@endsection