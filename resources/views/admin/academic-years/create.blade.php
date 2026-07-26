@extends('layouts.admin', ['title' => 'New Academic Year'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.academic-years.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Academic Years
    </a>
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">New Academic Year</h1>
    <p class="text-sm text-gray-500 mt-1">Create a new academic year period</p>
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

<form method="POST" action="{{ route('admin.academic-years.store') }}" class="space-y-5 max-w-2xl">
    @csrf

    {{-- Year Details --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <i class="ti ti-calendar text-amber-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Year Details</h2>
                <p class="text-xs text-gray-400">Name and date range for this academic year</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Year Name --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Year Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-calendar text-gray-400"></i>
                    </div>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. 2025-2026"
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

            {{-- Start Date --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Start Date <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-calendar-event text-gray-400"></i>
                    </div>
                    <input type="date" name="start_date" value="{{ old('start_date') }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('start_date')
                                      border-red-300 bg-red-50 focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border-gray-200 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('start_date')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- End Date --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    End Date <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-calendar-due text-gray-400"></i>
                    </div>
                    <input type="date" name="end_date" value="{{ old('end_date') }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('end_date')
                                      border-red-300 bg-red-50 focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border-gray-200 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('end_date')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>

    {{-- Activation --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                <i class="ti ti-toggle-right text-green-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Activation</h2>
                <p class="text-xs text-gray-400">Set this as the current active academic year</p>
            </div>
        </div>

        <div class="p-6">
            <label class="flex items-start gap-3 cursor-pointer p-4 rounded-xl border border-gray-100 
                          hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="is_active" value="1" 
                       {{ old('is_active') ? 'checked' : '' }}
                       class="w-5 h-5 rounded border-gray-300 text-green-600 
                              focus:ring-green-500 mt-0.5">
                <div>
                    <span class="text-sm font-semibold text-gray-700 block">
                        Set as active academic year
                    </span>
                    <span class="text-xs text-gray-400 mt-0.5 block">
                        This will deactivate any currently active year. Only one year can be active at a time.
                    </span>
                </div>
            </label>
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
            <a href="{{ route('admin.academic-years.index') }}"
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
                Save Year
            </button>
        </div>
    </div>

</form>

@endsection