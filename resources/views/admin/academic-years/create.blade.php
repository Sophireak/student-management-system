@extends('layouts.admin', ['title' => 'New Academic Year'])

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.academic-years.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Academic Years
    </a>
    <h1 class="text-2xl font-bold text-gray-800">New Academic Year</h1>
    <p class="text-sm text-gray-500 mt-1">Create a new academic year</p>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.academic-years.store') }}">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Year Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Name --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. 2025-2026"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('name')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                {{-- Start Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <div class="relative">
                        <i class="ti ti-calendar-event absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="date" name="start_date" value="{{ old('start_date') }}"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border-gray-300">
                    </div>
                </div>

                {{-- End Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <div class="relative">
                        <i class="ti ti-calendar-event absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="date" name="end_date" value="{{ old('end_date') }}"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border-gray-300">
                    </div>
                </div>

                {{-- Active --}}
                <div class="sm:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active') ? 'checked' : '' }}
                               class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                        <span class="text-sm font-medium text-gray-700">Set as active academic year</span>
                    </label>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="ti ti-device-floppy text-base"></i> Save Year
            </button>
            <a href="{{ route('admin.academic-years.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
