@extends('layouts.admin', ['title' => ''])

@section('content')

{{-- Header --}}
<div class="mb-6">
    <a href="{{ route('admin.students.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Students
    </a>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-user text-green-600 text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Student Informaion Editor
            </h1>
            <p class="text-sm text-gray-400 font-mono mt-0.5">{{ $student->student_id }}</p>
        </div>
    </div>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.students.update', $student) }}">
        @csrf
        @method('PUT')

        {{-- Basic Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Basic Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Student ID --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Student ID <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-id-badge absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="student_id"
                               value="{{ old('student_id', $student->student_id) }}"
                               required
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      {{ $errors->has('student_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('student_id')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- First Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="first_name"
                               value="{{ old('first_name', $student->first_name) }}"
                               required
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      {{ $errors->has('first_name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('first_name')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="last_name"
                               value="{{ old('last_name', $student->last_name) }}"
                               required
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      {{ $errors->has('last_name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('last_name')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <div class="relative">
                        <i class="ti ti-gender-bigender absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <select name="gender"
                                class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                       {{ $errors->has('gender') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                            <option value="">— Select gender —</option>
                            <option value="male"   {{ old('gender', $student->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    @error('gender')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Date of Birth --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <div class="relative">
                        <i class="ti ti-cake absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="date" name="date_of_birth"
                               value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      {{ $errors->has('date_of_birth') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('date_of_birth')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Guardian Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Guardian Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Name</label>
                    <div class="relative">
                        <i class="ti ti-users absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="guardian_name"
                               value="{{ old('guardian_name', $student->guardian_name) }}"
                               placeholder="Guardian full name"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border-gray-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Phone</label>
                    <div class="relative">
                        <i class="ti ti-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="guardian_phone"
                               value="{{ old('guardian_phone', $student->guardian_phone) }}"
                               placeholder="e.g. 012 345 678"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border-gray-300">
                    </div>
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="ti ti-device-floppy text-base"></i> Update Student
            </button>
            <a href="{{ route('admin.students.show', $student) }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
