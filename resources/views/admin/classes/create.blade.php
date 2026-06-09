@extends('layouts.admin', ['title' => 'New Class'])

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.classes.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Classes
    </a>
    <h1 class="text-2xl font-bold text-gray-800">New Class</h1>
    <p class="text-sm text-gray-500 mt-1">Fill in the details to create a new class</p>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.classes.store') }}">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Class Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Class Name --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class Name <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Class 1A"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('name')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                {{-- Grade --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Grade <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-award absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <select name="grade_id" required
                                class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('grade_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                            <option value="">— Select Grade —</option>
                            @foreach ($grades as $grade)
                                <option value="{{ $grade->id }}" {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('grade_id')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                {{-- Academic Year --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Academic Year <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <select name="academic_year_id" required
                                class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('academic_year_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                            <option value="">— Select Year —</option>
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('academic_year_id')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                {{-- Capacity --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                    <div class="relative">
                        <i class="ti ti-users absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="number" name="capacity" value="{{ old('capacity') }}" min="1" placeholder="e.g. 30"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border-gray-300">
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="ti ti-device-floppy text-base"></i> Save Class
            </button>
            <a href="{{ route('admin.classes.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
