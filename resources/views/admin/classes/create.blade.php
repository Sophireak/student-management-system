@extends('layouts.admin', ['title' => 'New Class'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.classes.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Classes
    </a>
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">New Class</h1>
    <p class="text-sm text-gray-500 mt-1">Fill in the details to create a new class</p>
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

<form method="POST" action="{{ route('admin.classes.store') }}" class="space-y-5 max-w-2xl">
    @csrf

    {{-- Class Information --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class="ti ti-building text-purple-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Class Information</h2>
                <p class="text-xs text-gray-400">Basic details about this class</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Class Name --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Class Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-building text-gray-400"></i>
                    </div>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. Class 1A"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('name')
                                      border-red-300 bg-red-50 focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border-gray-200 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Grade --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Grade <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-award text-gray-400"></i>
                    </div>
                    <select name="grade_id"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   @error('grade_id')
                                       border-red-300 bg-red-50
                                   @else
                                       border-gray-200 bg-gray-50 focus:bg-white 
                                       focus:border-green-500 focus:ring-2 focus:ring-green-100
                                   @enderror">
                        <option value="">Select grade</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->id }}" 
                                    {{ old('grade_id') == $grade->id ? 'selected' : '' }}>
                                {{ $grade->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('grade_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Academic Year --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Academic Year <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-calendar text-gray-400"></i>
                    </div>
                    <select name="academic_year_id"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   @error('academic_year_id')
                                       border-red-300 bg-red-50
                                   @else
                                       border-gray-200 bg-gray-50 focus:bg-white 
                                       focus:border-green-500 focus:ring-2 focus:ring-green-100
                                   @enderror">
                        <option value="">Select academic year</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}" 
                                    {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                                @if($year->is_active) (Active) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('academic_year_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Capacity --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Capacity
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-users text-gray-400"></i>
                    </div>
                    <input type="number" name="capacity" value="{{ old('capacity') }}"
                           min="1" placeholder="e.g. 30"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
                <p class="mt-1.5 text-xs text-gray-400">
                    Leave empty for unlimited capacity
                </p>
            </div>
            {{-- Session Period --}}
<div>
    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
        Session <span class="text-red-500">*</span>
    </label>
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
            <i class="ti ti-clock text-gray-400"></i>
        </div>
        <select name="session_period"
                class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                       appearance-none cursor-pointer
                       @error('session_period')
                           border-red-300 bg-red-50
                       @else
                           border-gray-200 bg-gray-50 focus:bg-white 
                           focus:border-green-500 focus:ring-2 focus:ring-green-100
                       @enderror">
            <option value="morning" {{ old('session_period', 'morning') === 'morning' ? 'selected' : '' }}>
                ព្រឹក (Morning) · 7:00 - 11:00
            </option>
            <option value="afternoon" {{ old('session_period') === 'afternoon' ? 'selected' : '' }}>
                រសៀល (Afternoon) · 13:00 - 17:00
            </option>
        </select>
    </div>
    @error('session_period')
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
            <a href="{{ route('admin.classes.index') }}"
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
                Save Class
            </button>
        </div>
    </div>

</form>

@endsection