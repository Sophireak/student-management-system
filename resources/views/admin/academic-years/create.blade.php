@extends('layouts.admin', ['title' => 'New Academic Year'])

@section('content')

<div class="max-w-lg">
    <a href="{{ route('admin.academic-years.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Academic Years
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">Create Academic Year</h2>

        <form method="POST" action="{{ route('admin.academic-years.store') }}" novalidate>
            @csrf

            {{-- Name --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Year Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="e.g. 2024-2025"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Start date --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Start Date <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       name="start_date"
                       value="{{ old('start_date') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('start_date') border-red-400 @enderror">
                @error('start_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- End date --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    End Date <span class="text-red-500">*</span>
                </label>
                <input type="date"
                       name="end_date"
                       value="{{ old('end_date') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('end_date') border-red-400 @enderror">
                @error('end_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Set as active --}}
            <div class="mb-6 flex items-center gap-2">
                <input type="checkbox"
                       name="is_active"
                       id="is_active"
                       value="1"
                       {{ old('is_active') ? 'checked' : '' }}
                       class="w-4 h-4 text-blue-600 border-gray-300 rounded">
                <label for="is_active" class="text-sm text-gray-700">
                    Set as active academic year
                    <span class="text-gray-400 text-xs">(will deactivate the current active year)</span>
                </label>
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Create Academic Year
            </button>
        </form>
    </div>
</div>

@endsection