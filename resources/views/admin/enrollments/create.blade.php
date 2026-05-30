@extends('layouts.admin', ['title' => 'Enroll Student'])

@section('content')

<div class="max-w-md">
    <a href="{{ route('admin.enrollments.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Enrollments
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-1">Enroll Student</h2>
        <p class="text-xs text-gray-400 mb-5">
            Only classes in the active academic year are shown.
            A student can only have one active enrollment per year.
        </p>

        <form method="POST" action="{{ route('admin.enrollments.store') }}" novalidate>
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Student <span class="text-red-500">*</span>
                </label>
                <select name="student_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('student_id') border-red-400 @enderror">
                    <option value="">— Select Student —</option>
                    @foreach ($students as $student)
                        <option value="{{ $student->id }}"
                            {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->full_name }}
                            ({{ $student->student_id }})
                        </option>
                    @endforeach
                </select>
                @error('student_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

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
                            {{ $class->name }}
                            — {{ $class->grade->name }}
                            ({{ $class->academicYear->name }})
                        </option>
                    @endforeach
                </select>
                @error('class_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Enrollment Date <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       name="enrolled_at"
                       value="{{ old('enrolled_at', now()->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('enrolled_at') border-red-400 @enderror">
                @error('enrolled_at')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Enroll Student
            </button>
        </form>
    </div>
</div>

@endsection