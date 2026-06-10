@extends('layouts.admin', ['title' => 'Edit Grade'])

@section('content')

{{-- Header --}}
<div class="mb-6">
    <a href="{{ route('admin.grades.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Grades
    </a>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-school text-green-600 text-xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Grade</h1>
            <p class="text-sm text-gray-400 font-mono mt-0.5">Level {{ $grade->level }} — {{ $grade->name }}</p>
        </div>
    </div>
</div>

<div class="max-w-2xl">
    <form method="POST" action="{{ route('admin.grades.update', $grade) }}">
        @csrf
        @method('PUT')

        {{-- Grade Info --}}
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Grade Information</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Level --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Level <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-sort-ascending-numbers absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="number" name="level"
                               value="{{ old('level', $grade->level) }}"
                               required min="1"
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      {{ $errors->has('level') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('level')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Grade Name <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i class="ti ti-school absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                        <input type="text" name="name"
                               value="{{ old('name', $grade->name) }}"
                               required
                               class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
                    </div>
                    @error('name')
                        <p class="mt-1 text-xs text-red-500 flex items-center gap-1"><i class="ti ti-alert-circle"></i> {{ $message }}</p>
                    @enderror
                </div>

            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3">
            <button type="submit"
                    class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <i class="ti ti-device-floppy text-base"></i> Update Grade
            </button>
            <a href="{{ route('admin.grades.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                Cancel
            </a>
        </div>

    </form>
</div>

@endsection
