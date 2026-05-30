@extends('layouts.admin', ['title' => 'New Teacher'])

@section('content')

<div class="max-w-xl">
    <a href="{{ route('admin.teachers.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Teachers
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-1">Create Teacher</h2>
        <p class="text-xs text-gray-400 mb-5">
            This will create a login account and teacher profile in one step.
        </p>

        <form method="POST" action="{{ route('admin.teachers.store') }}" novalidate>
            @csrf

            {{-- Section: Account --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                Login Account
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="e.g. Jane Smith"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('name') border-red-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       placeholder="e.g. jane@school.com"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       name="password"
                       placeholder="Minimum 8 characters"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password"
                       name="password_confirmation"
                       placeholder="Repeat password"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            {{-- Section: Profile --}}
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                Teacher Profile
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Employee ID
                    <span class="text-gray-400 text-xs font-normal">(optional)</span>
                </label>
                <input type="text"
                       name="employee_id"
                       value="{{ old('employee_id') }}"
                       placeholder="e.g. EMP-001"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('employee_id') border-red-400 @enderror">
                @error('employee_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
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

            <div class="mb-4">
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

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select name="gender"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address"
                          rows="2"
                          placeholder="e.g. 123 Street, Phnom Penh"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address') }}</textarea>
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Create Teacher
            </button>
        </form>
    </div>
</div>

@endsection