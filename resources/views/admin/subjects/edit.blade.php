@extends('layouts.admin', ['title' => 'Edit Subject'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.subjects.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Subjects
    </a>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-cyan-100 
                    text-blue-700 flex items-center justify-center shadow-inner flex-shrink-0">
            <i class="ti ti-book text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Edit Subject</h1>
            <p class="text-sm text-gray-400 mt-0.5 flex items-center gap-1.5">
                <i class="ti ti-book text-xs text-gray-300"></i>
                {{ $subject->name }}
                <span class="text-gray-300">·</span>
                <i class="ti ti-award text-xs text-gray-300"></i>
                {{ $subject->grade->name }}
                @if($subject->code)
                    <span class="text-gray-300">·</span>
                    <span class="font-mono text-xs">{{ $subject->code }}</span>
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

<form method="POST" action="{{ route('admin.subjects.update', $subject) }}" 
      class="space-y-5 max-w-2xl">
    @csrf
    @method('PUT')

    {{-- Subject Details --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ti ti-book text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Subject Details</h2>
                <p class="text-xs text-gray-400">Update subject information</p>
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
                    <input type="text" name="name" 
                           value="{{ old('name', $subject->name) }}"
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
                    <input type="text" name="code" 
                           value="{{ old('code', $subject->code) }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
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
                                    {{ old('grade_id', $subject->grade_id) == $grade->id ? 'selected' : '' }}>
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
                        <option value="numeric" 
                                {{ old('score_type', $subject->score_type) === 'numeric' ? 'selected' : '' }}>
                            Numeric (0-100)
                        </option>
                        <option value="grade" 
                                {{ old('score_type', $subject->score_type) === 'grade' ? 'selected' : '' }}>
                            Grade (A, B, C...)
                        </option>
                    </select>
                </div>
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
                    <input type="number" name="max_score" 
                           value="{{ old('max_score', $subject->max_score) }}"
                           min="1"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
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
                <p class="text-xs text-red-400">Irreversible actions</p>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 
                        p-4 rounded-xl bg-red-50/50 border border-red-100">
                <div>
                    <p class="text-sm font-semibold text-gray-800">Delete this subject</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        This will permanently delete the subject and all related score data.
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                      onsubmit="return confirm('Are you sure you want to delete {{ $subject->name }}? All related scores will be lost.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 
                                   text-red-600 text-sm font-semibold rounded-xl 
                                   hover:bg-red-50 hover:border-red-300 transition-all 
                                   active:scale-[0.98] whitespace-nowrap">
                        <i class="ti ti-trash text-lg"></i>
                        Delete Subject
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
            Last updated {{ $subject->updated_at->diffForHumans() }}
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
                Update Subject
            </button>
        </div>
    </div>

</form>

@endsection