@extends('layouts.admin', ['title' => 'Update Enrollment Status'])

@section('content')

<div class="max-w-md">
    <a href="{{ route('admin.enrollments.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Enrollments
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-1">Update Enrollment Status</h2>

        {{-- Read-only summary --}}
        <div class="mb-5 p-3 bg-gray-50 rounded-md text-sm text-gray-600 space-y-1">
            <p>
                <span class="font-medium">Student:</span>
                {{ $enrollment->student->full_name }}
            </p>
            <p>
                <span class="font-medium">Class:</span>
                {{ $enrollment->schoolClass->name }}
                — {{ $enrollment->schoolClass->grade->name }}
            </p>
            <p>
                <span class="font-medium">Year:</span>
                {{ $enrollment->schoolClass->academicYear->name }}
            </p>
        </div>

        <form method="POST"
              action="{{ route('admin.enrollments.update', $enrollment) }}"
              novalidate>
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Status <span class="text-red-500">*</span>
                </label>
                <select name="status"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500
                               @error('status') border-red-400 @enderror">
                    <option value="active"
                        {{ old('status', $enrollment->status) === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="transferred"
                        {{ old('status', $enrollment->status) === 'transferred' ? 'selected' : '' }}>
                        Transferred
                    </option>
                    <option value="dropped"
                        {{ old('status', $enrollment->status) === 'dropped' ? 'selected' : '' }}>
                        Dropped
                    </option>
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Update Status
            </button>
        </form>
    </div>
</div>

@endsection