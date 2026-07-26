@extends('layouts.admin', ['title' => 'Edit Class'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.classes.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Classes
    </a>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-100 to-indigo-100 
                    text-purple-700 flex items-center justify-center font-extrabold text-lg 
                    shadow-inner flex-shrink-0">
            {{ strtoupper(substr($class->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Edit Class</h1>
            <p class="text-sm text-gray-400 mt-0.5 flex items-center gap-1.5">
                <i class="ti ti-building text-xs text-gray-300"></i>
                {{ $class->name }}
                <span class="text-gray-300">·</span>
                <i class="ti ti-award text-xs text-gray-300"></i>
                {{ $class->grade->name }}
                <span class="text-gray-300">·</span>
                <i class="ti ti-calendar text-xs text-gray-300"></i>
                {{ $class->academicYear->name }}
            </p>
        </div>
    </div>
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

<form method="POST" action="{{ route('admin.classes.update', $class) }}" 
      class="space-y-5 max-w-2xl">
    @csrf
    @method('PUT')

    {{-- Class Information --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class="ti ti-building text-purple-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Class Information</h2>
                <p class="text-xs text-gray-400">Update the details for this class</p>
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
                    <input type="text" name="name" 
                           value="{{ old('name', $class->name) }}"
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
                                    {{ old('grade_id', $class->grade_id) == $grade->id ? 'selected' : '' }}>
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
                                    {{ old('academic_year_id', $class->academic_year_id) == $year->id ? 'selected' : '' }}>
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
                    <input type="number" name="capacity" 
                           value="{{ old('capacity', $class->capacity) }}"
                           min="1"
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
                       border-gray-200 bg-gray-50 focus:bg-white 
                       focus:border-green-500 focus:ring-2 focus:ring-green-100">
            <option value="morning" {{ old('session_period', $class->session_period) === 'morning' ? 'selected' : '' }}>
                ព្រឹក (Morning) · 7:00 - 11:00
            </option>
            <option value="afternoon" {{ old('session_period', $class->session_period) === 'afternoon' ? 'selected' : '' }}>
                រសៀល (Afternoon) · 13:00 - 17:00
            </option>
        </select>
    </div>
</div>

        </div>
    </div>

    {{-- Danger Zone --}}
    <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-red-50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                <i class="ti ti-alert-triangle text-red-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-red-700">Danger Zone</h2>
                <p class="text-xs text-red-400">Irreversible actions for this class</p>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 
                        p-4 rounded-xl bg-red-50/50 border border-red-100">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Delete this class</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        This will permanently delete the class and all related data.
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                      onsubmit="return confirm('Are you sure you want to delete {{ $class->name }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 
                                   text-red-600 text-sm font-semibold rounded-xl 
                                   hover:bg-red-50 hover:border-red-300 transition-all 
                                   active:scale-[0.98] whitespace-nowrap">
                        <i class="ti ti-trash text-lg"></i>
                        Delete Class
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-6 py-4 
                flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-gray-400 flex items-center gap-1.5">
            <i class="ti ti-info-circle text-gray-300"></i>
            Last updated {{ $class->updated_at->diffForHumans() }}
        </p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.classes.show', $class) }}"
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
                Update Class
            </button>
        </div>
    </div>

</form>

@endsection