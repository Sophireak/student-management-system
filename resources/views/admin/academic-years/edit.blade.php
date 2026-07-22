@extends('layouts.admin', ['title' => 'Edit Academic Year'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.academic-years.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Academic Years
    </a>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center 
                    font-extrabold text-lg shadow-inner flex-shrink-0
                    {{ $academicYear->is_active 
                        ? 'bg-gradient-to-br from-green-100 to-emerald-100 text-green-700' 
                        : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-500' }}">
            <i class="ti ti-calendar text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Edit Academic Year</h1>
            <p class="text-sm text-gray-400 mt-0.5 flex items-center gap-1.5">
                <i class="ti ti-calendar text-xs text-gray-300"></i>
                {{ $academicYear->name }}
                @if($academicYear->is_active)
                    <span class="text-gray-300">·</span>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] 
                                 font-bold bg-green-50 text-green-700 border border-green-100">
                        <span class="w-1 h-1 rounded-full bg-green-500"></span>
                        Active
                    </span>
                @endif
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

<form method="POST" action="{{ route('admin.academic-years.update', $academicYear) }}" 
      class="space-y-5 max-w-2xl">
    @csrf
    @method('PUT')

    {{-- Year Details --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <i class="ti ti-calendar text-amber-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Year Details</h2>
                <p class="text-xs text-gray-400">Update name and date range</p>
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
                    <input type="text" name="name" 
                           value="{{ old('name', $academicYear->name) }}"
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
                    <input type="date" name="start_date" 
                           value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d')) }}"
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
                    <input type="date" name="end_date" 
                           value="{{ old('end_date', $academicYear->end_date?->format('Y-m-d')) }}"
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
                <p class="text-xs text-gray-400">Control active status for this year</p>
            </div>
        </div>

        <div class="p-6">
            <label class="flex items-start gap-3 cursor-pointer p-4 rounded-xl border 
                          transition-colors
                          {{ $academicYear->is_active 
                              ? 'border-green-200 bg-green-50/50' 
                              : 'border-gray-100 hover:bg-gray-50' }}">
                <input type="checkbox" name="is_active" value="1"
                       {{ old('is_active', $academicYear->is_active) ? 'checked' : '' }}
                       class="w-5 h-5 rounded border-gray-300 text-green-600 
                              focus:ring-green-500 mt-0.5">
                <div>
                    <span class="text-sm font-semibold text-gray-700 block">
                        Set as active academic year
                    </span>
                    <span class="text-xs text-gray-400 mt-0.5 block">
                        This will deactivate any currently active year. Only one year can be active at a time.
                    </span>
                    @if($academicYear->is_active)
                        <span class="inline-flex items-center gap-1 mt-2 px-2 py-0.5 rounded-full text-[10px] 
                                     font-bold bg-green-100 text-green-700">
                            <i class="ti ti-check text-xs"></i>
                            Currently active
                        </span>
                    @endif
                </div>
            </label>
        </div>
    </div>

    {{-- Danger Zone --}}
    @if(!$academicYear->is_active)
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-red-50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-red-50 flex items-center justify-center">
                    <i class="ti ti-alert-triangle text-red-500 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-red-700">Danger Zone</h2>
                    <p class="text-xs text-red-400">Irreversible actions</p>
                </div>
            </div>
            <div class="p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 
                            p-4 rounded-xl bg-red-50/50 border border-red-100">
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Delete this academic year</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Only possible if no classes are assigned to this year.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('admin.academic-years.destroy', $academicYear) }}"
                          onsubmit="return confirm('Are you sure you want to delete {{ $academicYear->name }}? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 
                                       text-red-600 text-sm font-semibold rounded-xl 
                                       hover:bg-red-50 hover:border-red-300 transition-all 
                                       active:scale-[0.98] whitespace-nowrap">
                            <i class="ti ti-trash text-lg"></i>
                            Delete Year
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Form Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-6 py-4 
                flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-gray-400 flex items-center gap-1.5">
            <i class="ti ti-info-circle text-gray-300"></i>
            Last updated {{ $academicYear->updated_at->diffForHumans() }}
        </p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.academic-years.show', $academicYear) }}"
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
                Update Year
            </button>
        </div>
    </div>

</form>

@endsection