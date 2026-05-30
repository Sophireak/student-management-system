@extends('layouts.admin', ['title' => 'Edit Subject'])

@section('content')

<div class="max-w-md">
    <a href="{{ route('admin.subjects.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Subjects
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">Edit Subject</h2>

        <form method="POST"
              action="{{ route('admin.subjects.update', $subject) }}"
              novalidate>
            @csrf
            @method('PUT')

            {{-- Grade --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Grade <span class="text-red-500">*</span>
                </label>
                <select name="grade_id"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('grade_id') border-red-400 @enderror">
                    <option value="">— Select Grade —</option>
                    @foreach ($grades as $grade)
                        <option value="{{ $grade->id }}"
                            {{ old('grade_id', $subject->grade_id) == $grade->id ? 'selected' : '' }}>
                            {{ $grade->name }}
                        </option>
                    @endforeach
                </select>
                @error('grade_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Name --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Subject Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $subject->name) }}"
                       placeholder="e.g. Mathematics"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Code --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Subject Code
                    <span class="text-gray-400 text-xs font-normal">(optional)</span>
                </label>
                <input type="text"
                       name="code"
                       value="{{ old('code', $subject->code) }}"
                       placeholder="e.g. MATH-G1"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('code') border-red-400 @enderror">
                @error('code')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Update Subject
            </button>
        </form>
    </div>
</div>

@endsection