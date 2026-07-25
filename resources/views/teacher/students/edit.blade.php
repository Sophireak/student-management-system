@extends('layouts.admin', ['title' => 'Edit ' . $student->full_name])

@section('content')

{{-- Page Header --}}
<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 shadow-sm flex items-center gap-3">
    <a href="{{ route('teacher.students.show', $student) }}"
       class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors flex-shrink-0">
        <i class="ti ti-arrow-left text-base"></i>
    </a>
    <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
        <i class="ti ti-pencil text-green-600 text-xl"></i>
    </div>
    <h1 class="text-lg font-bold text-gray-800 leading-tight">Edit {{ $student->full_name }}</h1>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-5">Personal Information</h2>

        <form method="POST" action="{{ route('teacher.students.update', $student) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- First Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name"
                           value="{{ old('first_name', $student->first_name) }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                  @error('first_name') border-red-400 @enderror" />
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Last Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name"
                           value="{{ old('last_name', $student->last_name) }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                  @error('last_name') border-red-400 @enderror" />
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Date of Birth --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                  @error('date_of_birth') border-red-400 @enderror" />
                    @error('date_of_birth')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                   @error('gender') border-red-400 @enderror">
                        <option value="">— Select —</option>
                        <option value="male"   {{ old('gender', $student->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                    @error('gender')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                    <input type="text" name="phone"
                           value="{{ old('phone', $student->phone) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                  @error('phone') border-red-400 @enderror" />
                    @error('phone')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="address"
                           value="{{ old('address', $student->address) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                  @error('address') border-red-400 @enderror" />
                    @error('address')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Guardian Section --}}
            <div class="mt-6 pt-5 border-t border-gray-100">
                <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Guardian Information</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Name</label>
                        <input type="text" name="guardian_name"
                               value="{{ old('guardian_name', $student->guardian_name) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      @error('guardian_name') border-red-400 @enderror" />
                        @error('guardian_name')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Guardian Phone</label>
                        <input type="text" name="guardian_phone"
                               value="{{ old('guardian_phone', $student->guardian_phone) }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      @error('guardian_phone') border-red-400 @enderror" />
                        @error('guardian_phone')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                        <input type="text" name="guardian_relationship"
                               value="{{ old('guardian_relationship', $student->guardian_relationship) }}"
                               placeholder="e.g. Father, Mother, Uncle"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm
                                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                                      @error('guardian_relationship') border-red-400 @enderror" />
                        @error('guardian_relationship')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex items-center gap-3">
                <button type="submit"
                        class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700
                               text-white text-sm font-semibold rounded-full transition-colors">
                    <i class="ti ti-device-floppy text-base"></i> Save Changes
                </button>
                <a href="{{ route('teacher.students.show', $student) }}"
                   class="flex items-center gap-2 px-5 py-2.5 border border-gray-300 hover:bg-gray-50
                          text-gray-600 text-sm font-medium rounded-full transition-colors">
                    Cancel
                </a>
            </div>

        </form>
    </div>
</div>

@endsection