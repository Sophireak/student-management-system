@extends('layouts.admin', ['title' => 'New Teacher'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.teachers.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base 
                  group-hover:-translate-x-0.5 transition-transform">
        </i>
        Back to Teachers
    </a>
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
        New Teacher
    </h1>
    <p class="text-sm text-gray-500 mt-1">
        Fill in the details to register a new teaching staff member
    </p>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 
                rounded-2xl flex gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 
                    flex items-center justify-center flex-shrink-0">
            <i class="ti ti-alert-circle text-red-500 text-lg"></i>
        </div>
        <div>
            <p class="text-sm font-bold text-red-700 mb-1">
                Please fix {{ $errors->count() }} error(s):
            </p>
            <ul class="text-sm text-red-600 space-y-0.5 
                       list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<form method="POST" 
      action="{{ route('admin.teachers.store') }}" 
      class="space-y-5">
    @csrf

    {{-- Section 1: Account --}}
    <div class="bg-white rounded-2xl border border-gray-200 
                shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 
                    flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 
                        flex items-center justify-center">
                <i class="ti ti-lock text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">
                    Account Information
                </h2>
                <p class="text-xs text-gray-400">
                    Login credentials for this teacher
                </p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Full Name --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-user text-gray-400"></i>
                    </div>
                    <input type="text" name="name" 
                           value="{{ old('name') }}"
                           placeholder="Enter teacher's full name"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                  text-sm transition-all
                                  @error('name')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 
                                      focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 
                              flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-mail text-gray-400"></i>
                    </div>
                    <input type="email" name="email" 
                           value="{{ old('email') }}"
                           placeholder="teacher@school.com"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                  text-sm transition-all
                                  @error('email')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 
                                      focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 
                              flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Empty --}}
            <div class="hidden sm:block"></div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password"
                           placeholder="Minimum 8 characters"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                  text-sm transition-all
                                  @error('password')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 
                                      focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 
                              flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-lock-check text-gray-400"></i>
                    </div>
                    <input type="password" name="password_confirmation"
                           placeholder="Re-enter password"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                  text-sm transition-all
                                  border border-gray-200 bg-gray-50 
                                  focus:bg-white focus:border-green-500 
                                  focus:ring-2 focus:ring-green-100">
                </div>
            </div>

        </div>
    </div>

    {{-- Section 2: Personal Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 
                shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 
                    flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-50 
                        flex items-center justify-center">
                <i class="ti ti-user-circle text-green-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">
                    Personal Information
                </h2>
                <p class="text-xs text-gray-400">
                    Teacher's personal and contact details
                </p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Employee ID --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Employee ID
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-id-badge text-gray-400"></i>
                    </div>
                    <input type="text" name="employee_id" 
                           value="{{ old('employee_id') }}"
                           placeholder="e.g. EMP-001"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                  text-sm transition-all
                                  @error('employee_id')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 
                                      focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('employee_id')
                    <p class="mt-1.5 text-xs text-red-600 
                              flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Phone Number
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-phone text-gray-400"></i>
                    </div>
                    <input type="text" name="phone" 
                           value="{{ old('phone') }}"
                           placeholder="e.g. 012 345 678"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                  text-sm transition-all
                                  border border-gray-200 bg-gray-50 
                                  focus:bg-white focus:border-green-500 
                                  focus:ring-2 focus:ring-green-100">
                </div>
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Gender
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-gender-bigender text-gray-400"></i>
                    </div>
                    <select name="gender"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                   text-sm transition-all 
                                   appearance-none cursor-pointer
                                   @error('gender')
                                       border border-red-300 bg-red-50
                                   @else
                                       border border-gray-200 bg-gray-50 
                                       focus:bg-white focus:border-green-500 
                                       focus:ring-2 focus:ring-green-100
                                   @enderror">
                        <option value="">Select gender</option>
                        <option value="male" 
                            {{ old('gender') === 'male' ? 'selected' : '' }}>
                            Male
                        </option>
                        <option value="female" 
                            {{ old('gender') === 'female' ? 'selected' : '' }}>
                            Female
                        </option>
                    </select>
                </div>
                @error('gender')
                    <p class="mt-1.5 text-xs text-red-600 
                              flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Date of Birth --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Date of Birth
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-calendar text-gray-400"></i>
                    </div>
                    <input type="date" name="date_of_birth" 
                           value="{{ old('date_of_birth') }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                  text-sm transition-all
                                  @error('date_of_birth')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 
                                      focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('date_of_birth')
                    <p class="mt-1.5 text-xs text-red-600 
                              flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Address --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Address
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-0 pl-3.5 
                                flex items-start pointer-events-none">
                        <i class="ti ti-map-pin text-gray-400"></i>
                    </div>
                    <textarea name="address" rows="2"
                              placeholder="Enter teacher's home address"
                              class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                     text-sm transition-all resize-none
                                     border border-gray-200 bg-gray-50 
                                     focus:bg-white focus:border-green-500 
                                     focus:ring-2 focus:ring-green-100">{{ old('address') }}</textarea>
                </div>
            </div>

        </div>
    </div>

    {{-- Form Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 
                shadow-sm px-6 py-4 flex flex-col sm:flex-row 
                items-center justify-between gap-3">
        <p class="text-xs text-gray-400 flex items-center gap-1.5">
            <i class="ti ti-info-circle text-gray-300"></i>
            Fields marked with 
            <span class="text-red-500 font-bold mx-1">*</span> 
            are required
        </p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.teachers.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 
                      text-gray-600 text-sm font-semibold 
                      rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 
                           bg-green-600 hover:bg-green-700 text-white 
                           text-sm font-semibold rounded-xl transition-all 
                           shadow-sm hover:shadow-green-500/20 
                           active:scale-[0.98]">
                <i class="ti ti-device-floppy text-lg"></i>
                Save Teacher
            </button>
        </div>
    </div>

</form>

@endsection