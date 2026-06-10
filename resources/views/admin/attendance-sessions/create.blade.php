@extends('layouts.admin', ['title' => 'New Attendance Session'])

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.attendance-sessions.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Sessions
    </a>
    <h1 class="text-2xl font-bold text-gray-800">New Attendance Session</h1>
    <p class="text-sm text-gray-500 mt-1">Create a new attendance session for a class</p>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.attendance-sessions.store') }}">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Session Details</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Class --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Class <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <select name="school_class_id" required
                                class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('school_class_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                            <option value="">— Select Class —</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ old('school_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} — {{ $class->grade->name }} · {{ $class->academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('school_class_id')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                {{-- Subject --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-book absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <select name="subject_id" required
                                class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('subject_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                            <option value="">— Select Subject —</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('subject_id')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                {{-- Session Date --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Session Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="date" name="session_date"
                               value="{{ old('session_date', now()->format('Y-m-d')) }}"
                               required
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 {{ $errors->has('session_date') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('session_date')<p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>@enderror
                </div>

                {{-- Period --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                    <div class="relative">
                        <i class="ti ti-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <select name="period"
                                class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 border-gray-300">
                            <option value="">— Select Period —</option>
                            <option value="morning"   {{ old('period') === 'morning'   ? 'selected' : '' }}>Morning</option>
                            <option value="afternoon" {{ old('period') === 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="ti ti-device-floppy text-base"></i> Create Session
            </button>
            <a href="{{ route('admin.attendance-sessions.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
