@extends('layouts.admin', ['title' => 'Edit Class'])

@section('content')

<div class="max-w-md">
    <a href="{{ route('admin.classes.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Classes
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">Edit Class</h2>

        <form method="POST"
              action="{{ route('admin.classes.update', $class) }}"
              novalidate>
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Academic Year <span class="text-red-500">*</span>
                </label>
                <select name="academic_year_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('academic_year_id') border-red-400 @enderror">
                    @foreach ($academicYears as $year)
                        <option value="{{ $year->id }}"
                            {{ old('academic_year_id', $class->academic_year_id) == $year->id
                                ? 'selected' : '' }}>
                            {{ $year->name }}
                            {{ $year->is_active ? '(Active)' : '' }}
                        </option>
                    @endforeach
                </select>
                @error('academic_year_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Grade <span class="text-red-500">*</span>
                </label>
                <select name="grade_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('grade_id') border-red-400 @enderror">
                    @foreach ($grades as $grade)
                        <option value="{{ $grade->id }}"
                            {{ old('grade_id', $class->grade_id) == $grade->id
                                ? 'selected' : '' }}>
                            {{ $grade->name }}
                        </option>
                    @endforeach
                </select>
                @error('grade_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Class Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $class->name) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Capacity
                    <span class="text-gray-400 text-xs font-normal">(optional)</span>
                </label>
                <input type="number"
                       name="capacity"
                       value="{{ old('capacity', $class->capacity) }}"
                       min="1"
                       max="100"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('capacity') border-red-400 @enderror">
                @error('capacity')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Update Class
            </button>
        </form>
    </div>
</div>

@endsection