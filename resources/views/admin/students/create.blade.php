@extends('layouts.admin', ['title' => 'New Student'])

@section('content')

<div class="max-w-xl">
    <a href="{{ route('admin.students.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Students
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-1">Add New Student</h2>
        <p class="text-xs text-gray-400 mb-5">
            Student ID will be generated automatically.
            Class assignment is done separately via Enrollments.
        </p>

        <form method="POST" action="{{ route('admin.students.store') }}" novalidate>
            @csrf

            {{-- Personal Information --}}
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
                           value="{{ old('first_name') }}"
                           placeholder="e.g. Sophea"
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
                           value="{{ old('last_name') }}"
                           placeholder="e.g. Chan"
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
                           value="{{ old('date_of_birth') }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-blue-500
                                  @error('date_of_birth') border-red-400 @enderror">
                    @error('date_of_birth')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Gender
                    </label>
                    <select name="gender"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">— Select —</option>
                        <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text"
                       name="phone"
                       value="{{ old('phone') }}"
                       placeholder="e.g. +855 12 345 678"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address"
                          rows="2"
                          placeholder="e.g. 45 St 178, Phnom Penh"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
            </div>

            {{-- Guardian Information --}}
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
                           value="{{ old('guardian_name') }}"
                           placeholder="e.g. Chan Dara"
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
                        <option value="father" {{ old('guardian_relationship') === 'father' ? 'selected' : '' }}>Father</option>
                        <option value="mother" {{ old('guardian_relationship') === 'mother' ? 'selected' : '' }}>Mother</option>
                        <option value="other"  {{ old('guardian_relationship') === 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Guardian Phone
                </label>
                <input type="text"
                       name="guardian_phone"
                       value="{{ old('guardian_phone') }}"
                       placeholder="e.g. +855 12 999 888"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Create Student
            </button>
        </form>
    </div>
</div>

@endsection