@extends('layouts.admin', ['title' => 'Edit Grade'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.grades.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Grades
    </a>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-100 to-purple-100 
                    text-indigo-700 flex items-center justify-center font-extrabold text-lg 
                    shadow-inner flex-shrink-0">
            {{ $grade->level }}
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">Edit Grade</h1>
            <p class="text-sm text-gray-400 mt-0.5 flex items-center gap-1.5">
                <i class="ti ti-award text-xs text-gray-300"></i>
                {{ $grade->name }}
                <span class="text-gray-300">·</span>
                <span class="font-mono">Level {{ $grade->level }}</span>
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

<form method="POST" action="{{ route('admin.grades.update', $grade) }}" 
      class="space-y-5 max-w-2xl">
    @csrf
    @method('PUT')

    {{-- Grade Information --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                <i class="ti ti-award text-indigo-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Grade Information</h2>
                <p class="text-xs text-gray-400">Update the grade level and name</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Level --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Level <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-sort-ascending-numbers text-gray-400"></i>
                    </div>
                    <input type="number" name="level" 
                           value="{{ old('level', $grade->level) }}"
                           min="1"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('level')
                                      border-red-300 bg-red-50 focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border-gray-200 bg-gray-50 focus:bg-white focus:border-green-500 focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('level')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
                <p class="mt-1.5 text-xs text-gray-400">
                    Numeric order for sorting
                </p>
            </div>

            {{-- Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Grade Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-award text-gray-400"></i>
                    </div>
                    <input type="text" name="name" 
                           value="{{ old('name', $grade->name) }}"
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
                    <p class="text-sm font-semibold text-gray-800">Delete this grade</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Only possible if no classes or subjects are assigned.
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.grades.destroy', $grade) }}"
                      onsubmit="return confirm('Are you sure you want to delete {{ $grade->name }}? This cannot be undone.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-red-200 
                                   text-red-600 text-sm font-semibold rounded-xl 
                                   hover:bg-red-50 hover:border-red-300 transition-all 
                                   active:scale-[0.98] whitespace-nowrap">
                        <i class="ti ti-trash text-lg"></i>
                        Delete Grade
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
            Last updated {{ $grade->updated_at->diffForHumans() }}
        </p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.grades.index') }}"
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
                Update Grade
            </button>
        </div>
    </div>

</form>

@endsection