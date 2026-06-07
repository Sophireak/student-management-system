@extends('layouts.admin', ['title' => 'New Teacher'])

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.teachers.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Teachers
    </a>
    <h1 class="text-2xl font-bold text-gray-800">New Teacher</h1>
    <p class="text-sm text-gray-500 mt-1">Fill in the details to register a new teacher</p>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.teachers.store') }}">
        @csrf

        {{-- Account Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Account Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Teacher full name"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('name')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-mail absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="teacher@school.com"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('email')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="password" name="password" required placeholder="••••••••"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('password')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

            </div>
        </div>

        {{-- Teacher Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Teacher Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
                    <div class="relative">
                        <i class="ti ti-id-badge absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="employee_id" value="{{ old('employee_id') }}" placeholder="e.g. EMP-001"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border-gray-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <div class="relative">
                        <i class="ti ti-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="e.g. 012 345 678"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border-gray-300">
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="ti ti-device-floppy text-base"></i> Save Teacher
            </button>
            <a href="{{ route('admin.teachers.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
