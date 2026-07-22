@extends('layouts.admin', ['title' => 'Enroll Student'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.enrollments.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Enrollments
    </a>
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Enroll Student</h1>
    <p class="text-sm text-gray-500 mt-1">Assign a student to a class</p>
</div>

{{-- Validation Error Summary --}}
@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-alert-circle text-red-500 text-lg"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-red-700 mb-1">
                Please fix {{ $errors->count() }} error(s) before continuing:
            </p>
            <ul class="text-sm text-red-600 space-y-0.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('admin.enrollments.store') }}" class="space-y-5 max-w-2xl">
    @csrf

    {{-- Enrollment Details --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                <i class="ti ti-clipboard-list text-green-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Enrollment Details</h2>
                <p class="text-xs text-gray-400">Select student and class to enroll</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Student --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Student <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-user text-gray-400"></i>
                    </div>
                    <select name="student_id"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   @error('student_id')
                                       border-red-300 bg-red-50
                                   @else
                                       border-gray-200 bg-gray-50 focus:bg-white 
                                       focus:border-green-500 focus:ring-2 focus:ring-green-100
                                   @enderror">
                        <option value="">Select a student</option>
                        @foreach ($students as $student)
                            <option value="{{ $student->id }}" 
                                    {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->full_name }} ({{ $student->student_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('student_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-400">
                    Only showing students from the active academic year
                </p>
            </div>

            {{-- Class --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Class <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-building text-gray-400"></i>
                    </div>
                    <select name="school_class_id"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   @error('school_class_id')
                                       border-red-300 bg-red-50
                                   @else
                                       border-gray-200 bg-gray-50 focus:bg-white 
                                       focus:border-green-500 focus:ring-2 focus:ring-green-100
                                   @enderror">
                        <option value="">Select a class</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}" 
                                    {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }} — {{ $class->grade->name }} · {{ $class->academicYear->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('school_class_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-400">
                    Only showing classes from the active academic year
                </p>
            </div>

            {{-- Enrollment Date --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Enrollment Date
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-calendar text-gray-400"></i>
                    </div>
                    <input type="date" name="enrolled_at" 
                           value="{{ old('enrolled_at', now()->format('Y-m-d')) }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
                <p class="mt-1.5 text-xs text-gray-400">
                    Defaults to today's date
                </p>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Status
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-toggle-right text-gray-400"></i>
                    </div>
                    <select name="status"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   border-gray-200 bg-gray-50 focus:bg-white 
                                   focus:border-green-500 focus:ring-2 focus:ring-green-100">
                        <option value="active" 
                                {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="transferred" 
                                {{ old('status') === 'transferred' ? 'selected' : '' }}>
                            Transferred
                        </option>
                        <option value="dropped" 
                                {{ old('status') === 'dropped' ? 'selected' : '' }}>
                            Dropped
                        </option>
                    </select>
                </div>
                <p class="mt-1.5 text-xs text-gray-400">
                    Usually "Active" for new enrollments
                </p>
            </div>

        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex gap-3">
        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-info-circle text-blue-500 text-sm"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-blue-700 mb-0.5">Before enrolling</p>
            <p class="text-xs text-blue-600">
                Make sure the student is not already enrolled in another class 
                for the same academic year. Duplicate enrollments are not allowed.
            </p>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-6 py-4 
                flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-gray-400 flex items-center gap-1.5">
            <i class="ti ti-info-circle text-gray-300"></i>
            Fields marked with <span class="text-red-500 font-bold mx-1">*</span> are required
        </p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.enrollments.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 
                      text-sm font-semibold rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 
                           hover:bg-green-700 text-white text-sm font-semibold 
                           rounded-xl transition-all shadow-sm hover:shadow-green-500/20 
                           active:scale-[0.98]">
                <i class="ti ti-device-floppy text-lg"></i>
                Save Enrollment
            </button>
        </div>
    </div>

</form>

@endsection