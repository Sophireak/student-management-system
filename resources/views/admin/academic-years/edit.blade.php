@extends('layouts.admin', ['title' => 'Edit Academic Year'])

@section('content')

<div class="max-w-lg">
    <a href="{{ route('admin.academic-years.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Academic Years
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-4">Edit Academic Year</h2>

        <form method="POST"
              action="{{ route('admin.academic-years.update', $academicYear) }}"
              novalidate>
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Year Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $academicYear->name) }}"
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
                       value="{{ old('start_date', $academicYear->start_date->format('Y-m-d')) }}"
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
                       value="{{ old('end_date', $academicYear->end_date->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('end_date') border-red-400 @enderror">
                @error('end_date')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Active status notice --}}
            @if ($academicYear->is_active)
                <div class="mb-6 px-3 py-2 bg-green-50 border border-green-200 text-green-700 text-sm rounded-md">
                    ✅ This is the currently active academic year.
                    Use the <strong>Set Active</strong> button on the list to change it.
                </div>
            @endif

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                Update Academic Year
            </button>
        </form>
    </div>
</div>

@endsection