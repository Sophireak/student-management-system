@extends('layouts.admin', ['title' => 'Edit Teacher'])

@section('content')

<div class="max-w-xl">
    <a href="{{ route('admin.teachers.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Teachers
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-base font-semibold text-gray-700 mb-1">Edit Teacher</h2>
        <p class="text-xs text-gray-400 mb-5">
            Leave password blank to keep the current password.
        </p>

        <form method="POST"
              action="{{ route('admin.teachers.update', $teacher) }}"
              novalidate>
            @csrf
            @method('PUT')

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                Login Account
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $teacher->user->name) }}"
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
                       value="{{ old('email', $teacher->user->email) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500
                              @error('email') border-red-400 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    New Password
                    <span class="text-gray-400 text-xs font-normal">(leave blank to keep current)</span>
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
                    Confirm New Password
                </label>
                <input type="password"
                       name="password_confirmation"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
                Teacher Profile
            </p>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                <input type="text"
                       name="employee_id"
                       value="{{ old('employee_id', $teacher->employee_id) }}"
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
                       value="{{ old('phone', $teacher->phone) }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                              focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                <input type="date"
                       name="date_of_birth"
                       value="{{ old('date_of_birth', $teacher->date_of_birth?->format('Y-m-d')) }}"
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
                    <option value="male"   {{ old('gender', $teacher->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $teacher->gender) === 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea name="address"
                          rows="2"
                          class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('address', $teacher->address) }}</textarea>
            </div>

            <button type="submit"
                    class="w-full py-2 px-4 bg-blue-600 text-white text-sm font-medium
                           rounded-md hover:bg-blue-700">
                Update Teacher
            </button>
        </form>
    </div>
</div>

@endsection