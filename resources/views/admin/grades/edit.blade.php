@extends('layouts.admin', ['title' => 'Edit Grade'])

@section('content')

<div class="max-w-md">
    <a href="{{ route('admin.grades.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Grades
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">Edit Grade</h2>

        <form method="POST"
              action="{{ route('admin.grades.update', $grade) }}"
              novalidate>
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Grade Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $grade->name) }}"
                       placeholder="e.g. Grade 1"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Level <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       name="level"
                       value="{{ old('level', $grade->level) }}"
                       min="1"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('level') border-red-400 @enderror">
                @error('level')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Update Grade
            </button>
        </form>
    </div>
</div>

@endsection