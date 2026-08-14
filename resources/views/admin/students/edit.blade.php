@extends('layouts.admin', ['title' => 'Edit Student'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.students.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base 
                  group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Students
    </a>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center 
                    font-extrabold text-lg shadow-inner flex-shrink-0
                    {{ $student->gender === 'female' 
                        ? 'bg-gradient-to-br from-pink-100 to-rose-100 text-pink-700' 
                        : 'bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700' }}">
            {{ strtoupper(substr($student->first_name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                Edit Student
            </h1>
            <p class="text-sm text-gray-400 mt-0.5 flex items-center gap-1.5">
                <i class="ti ti-id-badge text-xs text-gray-300"></i>
                <span class="font-mono">{{ $student->student_id }}</span>
                <span class="text-gray-300">·</span>
                {{ $student->full_name }}
            </p>
        </div>
    </div>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 
                rounded-2xl flex gap-3">
        <div class="w-8 h-8 rounded-lg bg-red-100 
                    flex items-center justify-center flex-shrink-0">
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

{{-- ✅ Main Update Form — NO nested forms inside --}}
<form method="POST" 
      action="{{ route('admin.students.update', $student) }}" 
      enctype="multipart/form-data"
      class="space-y-5"
      id="update-form">
    @csrf
    @method('PUT')

    {{-- Section 0: Photo Upload --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden"
     x-data="{ 
        photoPreview: null,
        removeExisting: false,
        onPhotoChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.photoPreview = URL.createObjectURL(file);
                this.removeExisting = false;
            }
        },
        clearNewPhoto() {
            this.photoPreview = null;
            $refs.photoInput.value = '';
        }
     }">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg bg-pink-50 flex items-center justify-center">
            <i class="ti ti-camera text-pink-500 text-sm"></i>
        </div>
        <div>
            <h2 class="text-sm font-bold text-gray-700">Student Photo</h2>
            <p class="text-xs text-gray-400">Optional — JPG, PNG, WEBP · Max 2MB</p>
        </div>
    </div>

    <div class="p-6 flex flex-col sm:flex-row items-center gap-6">
        {{-- Preview --}}
        <div class="w-32 h-40 rounded-xl border-2 border-dashed border-gray-300 
                    bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
            {{-- New photo preview --}}
            <template x-if="photoPreview">
                <img :src="photoPreview" class="w-full h-full object-cover">
            </template>

            {{-- Existing photo (only if no new preview and not removing) --}}
            @if ($student->photo)
                <template x-if="!photoPreview && !removeExisting">
                    <img src="{{ asset('storage/' . $student->photo) }}" 
                         class="w-full h-full object-cover">
                </template>
            @endif

            {{-- Empty placeholder --}}
            <template x-if="!photoPreview && (removeExisting || {{ $student->photo ? 'false' : 'true' }})">
                <div class="text-center text-gray-400">
                    <i class="ti ti-photo text-4xl"></i>
                    <p class="text-xs mt-2">No photo</p>
                </div>
            </template>
        </div>

        {{-- Controls --}}
        <div class="flex-1 space-y-3 w-full">
            <div class="flex flex-wrap gap-2">
                <label class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 
                              text-gray-700 text-sm font-semibold rounded-xl cursor-pointer 
                              hover:bg-gray-50 transition-colors">
                    <i class="ti ti-upload text-base"></i>
                    {{ $student->photo ? 'Change Photo' : 'Choose Photo' }}
                    <input type="file" name="photo" accept="image/*" 
                           x-ref="photoInput"
                           @change="onPhotoChange($event)"
                           class="hidden">
                </label>

                {{-- Cancel new photo --}}
                <button type="button"
                        x-show="photoPreview"
                        @click="clearNewPhoto()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 
                               text-gray-600 text-sm font-semibold rounded-xl 
                               hover:bg-gray-200 transition-colors">
                    <i class="ti ti-x text-base"></i>
                    Cancel
                </button>

                {{-- Remove existing photo --}}
                @if ($student->photo)
                    <button type="button"
                            x-show="!photoPreview && !removeExisting"
                            @click="removeExisting = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 
                                   text-red-600 text-sm font-semibold rounded-xl 
                                   hover:bg-red-100 transition-colors">
                        <i class="ti ti-trash text-base"></i>
                        Remove Current
                    </button>

                    <button type="button"
                            x-show="removeExisting"
                            @click="removeExisting = false"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 
                                   text-gray-600 text-sm font-semibold rounded-xl 
                                   hover:bg-gray-200 transition-colors">
                        <i class="ti ti-arrow-back-up text-base"></i>
                        Undo
                    </button>
                @endif
            </div>

            {{-- Hidden field for removal --}}
            <input type="hidden" name="remove_photo" :value="removeExisting ? '1' : '0'">

            @error('photo')
                <p class="text-xs text-red-600 flex items-center gap-1">
                    <i class="ti ti-alert-circle"></i> {{ $message }}
                </p>
            @enderror

            <p class="text-xs text-gray-400">
                <i class="ti ti-info-circle"></i>
                Photo will be shown in reports and student list.
            </p>
        </div>
    </div>
</div>
    {{-- Section 1: Identity --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ti ti-id-badge text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Student Identity</h2>
                <p class="text-xs text-gray-400">Basic identification information</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Student ID --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Student ID <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-id-badge text-gray-400"></i>
                    </div>
                    <input type="text" name="student_id"
                           value="{{ old('student_id', $student->student_id) }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('student_id')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('student_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- First Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    First Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-user text-gray-400"></i>
                    </div>
                    <input type="text" name="first_name"
                           value="{{ old('first_name', $student->first_name) }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('first_name')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('first_name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Last Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-user text-gray-400"></i>
                    </div>
                    <input type="text" name="last_name"
                           value="{{ old('last_name', $student->last_name) }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('last_name')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('last_name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Gender
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-gender-bigender text-gray-400"></i>
                    </div>
                    <select name="gender"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm 
                                   transition-all appearance-none cursor-pointer
                                   border border-gray-200 bg-gray-50 
                                   focus:bg-white focus:border-green-500 
                                   focus:ring-2 focus:ring-green-100">
                        <option value="">Select gender</option>
                        <option value="male" 
                            {{ old('gender', $student->gender) === 'male' 
                                ? 'selected' : '' }}>
                            Male
                        </option>
                        <option value="female" 
                            {{ old('gender', $student->gender) === 'female' 
                                ? 'selected' : '' }}>
                            Female
                        </option>
                    </select>
                </div>
            </div>

            {{-- Date of Birth --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Date of Birth
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-cake text-gray-400"></i>
                    </div>
                    <input type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('date_of_birth')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('date_of_birth')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>

    {{-- Section 2: Contact --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                <i class="ti ti-phone text-green-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Contact Information</h2>
                <p class="text-xs text-gray-400">Student's phone and address</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Phone Number
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-phone text-gray-400"></i>
                    </div>
                    <input type="text" name="phone"
                           value="{{ old('phone', $student->phone) }}"
                           placeholder="e.g. 012 345 678"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
            </div>

            <div class="hidden sm:block"></div>

            {{-- Address --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Address
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-0 pl-3.5 
                                flex items-start pointer-events-none">
                        <i class="ti ti-map-pin text-gray-400"></i>
                    </div>
                    <textarea name="address" rows="2"
                              placeholder="Enter student's home address"
                              class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                     text-sm transition-all resize-none
                                     border border-gray-200 bg-gray-50 
                                     focus:bg-white focus:border-green-500 
                                     focus:ring-2 focus:ring-green-100"
                    >{{ old('address', $student->address) }}</textarea>
                </div>
            </div>

        </div>
    </div>

    {{-- Section 3: Guardian --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class="ti ti-user-heart text-purple-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Guardian Information</h2>
                <p class="text-xs text-gray-400">Parent or guardian contact details</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Guardian Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Guardian Name
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-users text-gray-400"></i>
                    </div>
                    <input type="text" name="guardian_name"
                           value="{{ old('guardian_name', $student->guardian_name) }}"
                           placeholder="Guardian full name"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
            </div>

            {{-- Guardian Phone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Guardian Phone
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-phone text-gray-400"></i>
                    </div>
                    <input type="text" name="guardian_phone"
                           value="{{ old('guardian_phone', $student->guardian_phone) }}"
                           placeholder="e.g. 012 345 678"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border border-gray-200 bg-gray-50 focus:bg-white 
                                  focus:border-green-500 focus:ring-2 focus:ring-green-100">
                </div>
            </div>

            {{-- Relationship --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Relationship
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (optional)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-heart text-gray-400"></i>
                    </div>
                    <select name="guardian_relationship"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm 
                                   transition-all appearance-none cursor-pointer
                                   border border-gray-200 bg-gray-50 
                                   focus:bg-white focus:border-green-500 
                                   focus:ring-2 focus:ring-green-100">
                        <option value="">Select relationship</option>
                        <option value="father" 
                            {{ old('guardian_relationship', $student->guardian_relationship) === 'father' 
                                ? 'selected' : '' }}>
                            Father
                        </option>
                        <option value="mother" 
                            {{ old('guardian_relationship', $student->guardian_relationship) === 'mother' 
                                ? 'selected' : '' }}>
                            Mother
                        </option>
                        <option value="other" 
                            {{ old('guardian_relationship', $student->guardian_relationship) === 'other' 
                                ? 'selected' : '' }}>
                            Other
                        </option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    {{-- Form Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm 
                px-6 py-4 flex flex-col sm:flex-row 
                items-center justify-between gap-3">
        <p class="text-xs text-gray-400 flex items-center gap-1.5">
            <i class="ti ti-info-circle text-gray-300"></i>
            Last updated {{ $student->updated_at->diffForHumans() }}
        </p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.students.show', $student) }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 
                      text-gray-600 text-sm font-semibold 
                      rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 
                           bg-green-600 hover:bg-green-700 text-white 
                           text-sm font-semibold rounded-xl transition-all 
                           shadow-sm hover:shadow-green-500/20 
                           active:scale-[0.98]">
                <i class="ti ti-device-floppy text-lg"></i>
                Update Student
            </button>
        </div>
    </div>

</form>
{{-- ✅ Danger Zone is NOW OUTSIDE the main form --}}
<div x-data="{ archiveModal: false }" class="mt-5">

    <div class="bg-white rounded-2xl border border-red-100 
                shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b border-red-50 
                    flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-50 
                        flex items-center justify-center">
                <i class="ti ti-alert-triangle text-red-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-red-700">Danger Zone</h2>
                <p class="text-xs text-red-400">
                    Irreversible actions for this student
                </p>
            </div>
        </div>

        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center 
                        sm:justify-between gap-4 
                        p-4 rounded-xl bg-red-50/50 border border-red-100">
                <div>
                    <p class="text-sm font-semibold text-gray-800">
                        Archive this student
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Removes student from active lists. 
                        Can be restored later.
                    </p>
                </div>
                <button
                    type="button"
                    @click="archiveModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 
                           bg-white border border-red-200 text-red-600 
                           text-sm font-semibold rounded-xl 
                           hover:bg-red-50 hover:border-red-300 
                           transition-all active:scale-[0.98] 
                           whitespace-nowrap">
                    <i class="ti ti-archive text-lg"></i>
                    Archive Student
                </button>
            </div>
        </div>
    </div>

    {{-- Archive Modal --}}
    <div
        x-show="archiveModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center 
               justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="archiveModal = false"
        @keydown.escape.window="archiveModal = false"
    >
        <div
            x-show="archiveModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm"
        >
            <div class="w-12 h-12 rounded-2xl bg-red-50 
                        flex items-center justify-center mx-auto mb-4">
                <i class="ti ti-archive text-red-500 text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800 text-center mb-1">
                Archive Student?
            </h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Are you sure you want to archive
                <span class="font-semibold text-gray-700">
                    {{ $student->full_name }}
                </span>?
                You can restore them later.
            </p>
            <div class="flex gap-3">
                <button
                    @click="archiveModal = false"
                    type="button"
                    class="flex-1 px-4 py-2.5 bg-gray-100 
                           hover:bg-gray-200 text-gray-600 
                           text-sm font-semibold rounded-xl 
                           transition-colors">
                    Cancel
                </button>
                {{-- ✅ This form is now completely standalone --}}
                <form method="POST"
                      action="{{ route('admin.students.destroy', $student) }}"
                      class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-red-500 
                                   hover:bg-red-600 text-white 
                                   text-sm font-semibold rounded-xl 
                                   transition-colors">
                        Yes, Archive
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection