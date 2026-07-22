@extends('layouts.admin', ['title' => 'New Student'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.students.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Students
    </a>
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">New Student</h1>
    {{-- ✅ Fixed typo: 'det fails' → 'details' --}}
    <p class="text-sm text-gray-500 mt-1">Fill in the details to register a new student</p>
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

<form method="POST" action="{{ route('admin.students.store') }}" class="space-y-5">
    @csrf

    {{-- Section 1: Identity --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ti ti-id-badge text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Student Identity</h2>
                <p class="text-xs text-gray-400">Basic identification information</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Student ID --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Student ID <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-id-badge text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border' --}}
                    <input type="text" name="student_id" value="{{ old('student_id') }}"
                           placeholder="e.g. STU-2024-001"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('student_id')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('student_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- First Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    First Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-user text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border' --}}
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                           placeholder="First name"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('first_name')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('first_name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Last Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-user text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border' --}}
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           placeholder="Last name"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('last_name')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('last_name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Gender
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-gender-bigender text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border', added 'border' to error state --}}
                    <select name="gender"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   @error('gender')
                                       border border-red-300 bg-red-50
                                   @else
                                       border border-gray-200 bg-gray-50 
                                       focus:bg-white focus:border-green-500 
                                       focus:ring-2 focus:ring-green-100
                                   @enderror">
                        <option value="">Select gender</option>
                        <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>
                @error('gender')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Date of Birth --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Date of Birth
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-cake text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border' --}}
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('date_of_birth')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('date_of_birth')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>

    {{-- Section 2: Contact Information --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                <i class="ti ti-phone text-green-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Contact Information</h2>
                <p class="text-xs text-gray-400">Student's phone and address</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Phone Number
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-phone text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border' --}}
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           placeholder="e.g. 012 345 678"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
            </div>

            {{-- Empty column --}}
            <div class="hidden sm:block"></div>

            {{-- Address --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Address
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-0 pl-3.5 flex items-start pointer-events-none">
                        <i class="ti ti-map-pin text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border' --}}
                    <textarea name="address" rows="2"
                              placeholder="Enter student's home address"
                              class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all resize-none
                                     border border-gray-200 bg-gray-50 focus:bg-white 
                                     focus:border-green-500 focus:ring-2 focus:ring-green-100">{{ old('address') }}</textarea>
                </div>
            </div>

        </div>
    </div>

    {{-- Section 3: Guardian Information --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class="ti ti-user-heart text-purple-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Guardian Information</h2>
                <p class="text-xs text-gray-400">Parent or guardian contact details</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Guardian Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Guardian Name
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-users text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border' --}}
                    <input type="text" name="guardian_name" value="{{ old('guardian_name') }}"
                           placeholder="Guardian full name"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
            </div>

            {{-- Guardian Phone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Guardian Phone
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-phone text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border' --}}
                    <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}"
                           placeholder="e.g. 012 345 678"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
            </div>

            {{-- Guardian Relationship --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Relationship
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-heart text-gray-400"></i>
                    </div>
                    {{-- ✅ Fixed: only one 'border', added 'border' to error state --}}
                    <select name="guardian_relationship"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   @error('guardian_relationship')
                                       border border-red-300 bg-red-50
                                   @else
                                       border border-gray-200 bg-gray-50 
                                       focus:bg-white focus:border-green-500 
                                       focus:ring-2 focus:ring-green-100
                                   @enderror">
                        <option value="">Select relationship</option>
                        <option value="father" {{ old('guardian_relationship') === 'father' ? 'selected' : '' }}>Father</option>
                        <option value="mother" {{ old('guardian_relationship') === 'mother' ? 'selected' : '' }}>Mother</option>
                        <option value="other"  {{ old('guardian_relationship') === 'other'  ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                @error('guardian_relationship')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

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
            <a href="{{ route('admin.students.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 
                      text-sm font-semibold rounded-xl transition-colors">
                Cancel
            </a>
            {{-- ✅ Added loading state on submit --}}
            <button type="submit"
                    x-data
                    @click="$el.disabled = true; $el.innerHTML = 
                        '<i class=\'ti ti-loader-2 animate-spin text-lg\'></i> Saving...'; 
                        $el.closest('form').submit()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 
                           hover:bg-green-700 text-white text-sm font-semibold 
                           rounded-xl transition-all shadow-sm hover:shadow-green-500/20 
                           active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed">
                <i class="ti ti-device-floppy text-lg"></i>
                Save Student
            </button>
        </div>
    </div>

</form>

@endsection