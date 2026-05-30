@extends('layouts.admin', ['title' => 'Assign Teacher'])

@section('content')

<div class="max-w-md">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('admin.classes.index') }}" class="hover:text-gray-700">Classes</a>
        <span>›</span>
        <a href="{{ route('admin.classes.show', $class) }}"
           class="hover:text-gray-700">{{ $class->name }}</a>
        <span>›</span>
        <a href="{{ route('admin.classes.teachers.index', $class) }}"
           class="hover:text-gray-700">Teachers</a>
        <span>›</span>
        <span class="text-gray-700">Assign</span>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        {{-- Class context --}}
        <div class="mb-5 p-3 bg-gray-50 rounded-md text-sm text-gray-600 space-y-1">
            <p class="font-semibold text-gray-700">
                Assigning teacher to: Class {{ $class->name }}
            </p>
            <p class="text-xs text-gray-400">
                {{ $class->grade->name }}
                · {{ $class->academicYear->name }}
            </p>
        </div>

        @if ($teachers->isEmpty())
            <div class="text-sm text-gray-400 text-center py-4">
                All available teachers are already assigned to this class.
            </div>
        @else
            <form method="POST"
                  action="{{ route('admin.classes.teachers.store', $class) }}"
                  novalidate>
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Teacher <span class="text-red-500">*</span>
                    </label>
                    <select name="teacher_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500
                                   @error('teacher_id') border-red-400 @enderror">
                        <option value="">— Select Teacher —</option>
                        @foreach ($teachers as $teacher)
                            <option value="{{ $teacher->id }}"
                                {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->user->name }}
                                @if ($teacher->employee_id)
                                    ({{ $teacher->employee_id }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6 flex items-start gap-2">
                    <input type="checkbox"
                           name="is_primary"
                           id="is_primary"
                           value="1"
                           {{ old('is_primary') ? 'checked' : '' }}
                           class="mt-0.5 w-4 h-4 text-blue-600 border-gray-300 rounded">
                    <div>
                        <label for="is_primary"
                               class="text-sm font-medium text-gray-700">
                            Set as primary teacher
                        </label>
                        <p class="text-xs text-gray-400 mt-0.5">
                            The primary teacher is the homeroom/main teacher for this class.
                            Any existing primary teacher will be demoted automatically.
                        </p>
                    </div>
                </div>

                <button type="submit"
                        class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                               rounded-md hover:bg-blue-700">
                    Assign Teacher
                </button>
            </form>
        @endif

    </div>
</div>

@endsection