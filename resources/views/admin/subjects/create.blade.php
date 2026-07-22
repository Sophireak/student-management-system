@extends('layouts.admin', ['title' => 'New Subject'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.subjects.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Subjects
    </a>
    <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">New Subject</h1>
    <p class="text-sm text-gray-500 mt-1">Add a new subject to the system</p>
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

<form method="POST" action="{{ route('admin.subjects.store') }}" class="space-y-5 max-w-2xl">
    @csrf

    {{-- Subject Details --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ti ti-book text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Subject Details</h2>
                <p class="text-xs text-gray-400">Basic information about this subject</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Name --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Subject Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-book text-gray-400"></i>
                    </div>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="e.g. គណិតវិទ្យា"
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

            {{-- Code --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Subject Code
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-hash text-gray-400"></i>
                    </div>
                    <input type="text" name="code" value="{{ old('code') }}"
                           placeholder="e.g. MATH01"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
                <p class="mt-1.5 text-xs text-gray-400">
                    Short code for quick identification
                </p>
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

        </div>
    </div>

    {{-- Scoring --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class="ti ti-chart-bar text-purple-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Scoring Configuration</h2>
                <p class="text-xs text-gray-400">How this subject is scored</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Score Type --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Score Type
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-chart-bar text-gray-400"></i>
                    </div>
                    <select name="score_type"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   border-gray-200 bg-gray-50 focus:bg-white 
                                   focus:border-green-500 focus:ring-2 focus:ring-green-100">
                        <option value="numeric" {{ old('score_type', 'numeric') === 'numeric' ? 'selected' : '' }}>
                            Numeric (0-100)
                        </option>
                        <option value="grade" {{ old('score_type') === 'grade' ? 'selected' : '' }}>
                            Grade (A, B, C...)
                        </option>
                    </select>
                </div>
                <p class="mt-1.5 text-xs text-gray-400">
                    Choose how scores are entered for this subject
                </p>
            </div>

            {{-- Max Score --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Max Score
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-target text-gray-400"></i>
                    </div>
                    <input type="number" name="max_score" value="{{ old('max_score', 100) }}"
                           min="1"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
                <p class="mt-1.5 text-xs text-gray-400">
                    Maximum score for numeric type (usually 100)
                </p>
            </div>

        </div>
    </div>

    {{-- Tip Box --}}
    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-4 flex gap-3">
        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-bulb text-amber-600 text-sm"></i>
        </div>
        <div>
            <p class="text-sm font-semibold text-amber-700 mb-0.5">Quick Tip</p>
            <p class="text-xs text-amber-600">
                Want to add all standard Cambodia primary school subjects at once? 
                Use the <strong>Auto-Fill Template</strong> button on the subjects index page instead!
            </p>
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
            <a href="{{ route('admin.subjects.index') }}"
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
                Save Subject
            </button>
        </div>
    </div>

</form>

@endsection