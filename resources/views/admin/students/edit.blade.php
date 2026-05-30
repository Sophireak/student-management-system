@extends('layouts.admin', ['title' => 'Edit Student'])

@section('content')

<div class="max-w-xl">
    <a href="{{ route('admin.students.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Students
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

        {{-- Student ID badge — read only --}}
        <div class="flex items-center justify-between mb-1">
            <h2 class="text-base font-semibold text-gray-700">Edit Student</h2>
            <span class="font-mono text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded">
                {{ $student->student_id }}
            </span>
        </div>
        <p class="text-xs text-gray-400 mb-5">Student ID cannot be changed.</p>

        <form method="POST"
              action="{{ route('admin.students.update', $student) }}"
              novalidate>
            @csrf
            @method('PUT')

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                Personal Information
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="first_name"
                           value="{{ old('first_name', $student->first_name) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('first_name') border-red-400 @enderror">
                    @error('first_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="last_name"
                           value="{{ old('last_name', $student->last_name) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('last_name') border-red-400 @enderror">
                    @error('last_name')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date of Birth
                    </label>
                    <input type="date"
                           name="date_of_birth"
                           value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('date_of_birth') border-red-400 @enderror">
                    @error('date_of_birth')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        <option value="male"   {{ old('gender', $student->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text"
                       name="phone"
                       value="{{ old('phone', $student->phone) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address"
                          rows="2"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $student->address) }}</textarea>
            </div>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                Guardian Information
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Guardian Name
                    </label>
                    <input type="text"
                           name="guardian_name"
                           value="{{ old('guardian_name', $student->guardian_name) }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Relationship
                    </label>
                    <select name="guardian_relationship"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        <option value="father" {{ old('guardian_relationship', $student->guardian_relationship) === 'father' ? 'selected' : '' }}>Father</option>
                        <option value="mother" {{ old('guardian_relationship', $student->guardian_relationship) === 'mother' ? 'selected' : '' }}>Mother</option>
                        <option value="other"  {{ old('guardian_relationship', $student->guardian_relationship) === 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Guardian Phone
                </label>
                <input type="text"
                       name="guardian_phone"
                       value="{{ old('guardian_phone', $student->guardian_phone) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Update Student
            </button>
        </form>
    </div>
</div>

@endsection