@extends('layouts.teacher', ['title' => 'Edit ' . $student->full_name])

@section('content')

{{-- Back Link --}}
<a href="{{ route('teacher.students.show', $student) }}"
   class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="ti ti-arrow-left text-base"></i> Back to Profile
</a>

<form method="POST" 
      action="{{ route('teacher.students.update', $student) }}" 
      enctype="multipart/form-data"
      class="space-y-5">
    @csrf
    @method('PUT')

    {{-- Photo Upload --}}
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
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-pink-50 flex items-center justify-center">
                <i class="ti ti-camera text-pink-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Student Photo</h2>
        </div>

        <div class="p-5 flex flex-col sm:flex-row items-center gap-5">
            {{-- Preview --}}
            <div class="w-24 h-32 rounded-xl border-2 border-dashed border-gray-300 
                        bg-gray-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                <template x-if="photoPreview">
                    <img :src="photoPreview" class="w-full h-full object-cover">
                </template>

                @if ($student->photo)
                    <template x-if="!photoPreview && !removeExisting">
                        <img src="{{ asset('storage/' . $student->photo) }}" 
                             class="w-full h-full object-cover">
                    </template>
                @endif

                <template x-if="!photoPreview && (removeExisting || {{ $student->photo ? 'false' : 'true' }})">
                    <div class="text-center text-gray-400">
                        <i class="ti ti-photo text-3xl"></i>
                    </div>
                </template>
            </div>

            <div class="flex-1 space-y-2 w-full">
                <div class="flex flex-wrap gap-2">
                    <label class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 
                                  text-gray-700 text-sm font-semibold rounded-xl cursor-pointer 
                                  hover:bg-gray-50 transition-colors">
                        <i class="ti ti-upload text-base"></i>
                        {{ $student->photo ? 'Change' : 'Choose' }}
                        <input type="file" name="photo" accept="image/*" 
                               x-ref="photoInput"
                               @change="onPhotoChange($event)"
                               class="hidden">
                    </label>

                    <button type="button"
                            x-show="photoPreview"
                            @click="clearNewPhoto()"
                            class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 
                                   text-gray-600 text-sm font-semibold rounded-xl 
                                   hover:bg-gray-200 transition-colors">
                        <i class="ti ti-x text-base"></i>
                    </button>

                    @if ($student->photo)
                        <button type="button"
                                x-show="!photoPreview && !removeExisting"
                                @click="removeExisting = true"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-red-50 border border-red-200 
                                       text-red-600 text-sm font-semibold rounded-xl 
                                       hover:bg-red-100 transition-colors">
                            <i class="ti ti-trash text-base"></i>
                            Remove
                        </button>

                        <button type="button"
                                x-show="removeExisting"
                                @click="removeExisting = false"
                                class="inline-flex items-center gap-2 px-3 py-2 bg-gray-100 
                                       text-gray-600 text-sm font-semibold rounded-xl 
                                       hover:bg-gray-200 transition-colors">
                            Undo
                        </button>
                    @endif
                </div>

                <input type="hidden" name="remove_photo" :value="removeExisting ? '1' : '0'">

                <p class="text-xs text-gray-400">
                    JPG, PNG, WEBP · Max 2MB
                </p>

                @error('photo')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Personal Information --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ti ti-id-badge text-blue-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Personal Information</h2>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- First Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="first_name"
                       value="{{ old('first_name', $student->first_name) }}"
                       required
                       class="w-full rounded-xl px-3 py-2.5 text-sm transition-all
                              @error('first_name')
                                  border border-red-300 bg-red-50 
                              @else
                                  border border-gray-200 bg-gray-50 
                                  focus:bg-white focus:border-green-500 
                                  focus:ring-2 focus:ring-green-100
                              @enderror">
                @error('first_name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Last Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="last_name"
                       value="{{ old('last_name', $student->last_name) }}"
                       required
                       class="w-full rounded-xl px-3 py-2.5 text-sm transition-all
                              @error('last_name')
                                  border border-red-300 bg-red-50 
                              @else
                                  border border-gray-200 bg-gray-50 
                                  focus:bg-white focus:border-green-500 
                                  focus:ring-2 focus:ring-green-100
                              @enderror">
                @error('last_name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Date of Birth --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Date of Birth
                </label>
                <input type="date" name="date_of_birth"
                       value="{{ old('date_of_birth', $student->date_of_birth?->format('Y-m-d')) }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm transition-all
                              border border-gray-200 bg-gray-50 
                              focus:bg-white focus:border-green-500 
                              focus:ring-2 focus:ring-green-100">
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Gender
                </label>
                <select name="gender"
                        class="w-full rounded-xl px-3 py-2.5 text-sm transition-all appearance-none cursor-pointer
                               border border-gray-200 bg-gray-50 
                               focus:bg-white focus:border-green-500 
                               focus:ring-2 focus:ring-green-100">
                    <option value="">— Select —</option>
                    <option value="male"   {{ old('gender', $student->gender) === 'male'   ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $student->gender) === 'female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Phone</label>
                <input type="text" name="phone"
                       value="{{ old('phone', $student->phone) }}"
                       placeholder="e.g. 012 345 678"
                       class="w-full rounded-xl px-3 py-2.5 text-sm transition-all
                              border border-gray-200 bg-gray-50 
                              focus:bg-white focus:border-green-500 
                              focus:ring-2 focus:ring-green-100">
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Address</label>
                <input type="text" name="address"
                       value="{{ old('address', $student->address) }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm transition-all
                              border border-gray-200 bg-gray-50 
                              focus:bg-white focus:border-green-500 
                              focus:ring-2 focus:ring-green-100">
            </div>

        </div>
    </div>

    {{-- Guardian Information --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class="ti ti-user-heart text-purple-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Guardian Information</h2>
        </div>

        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4">

            {{-- Guardian Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Guardian Name
                </label>
                <input type="text" name="guardian_name"
                       value="{{ old('guardian_name', $student->guardian_name) }}"
                       class="w-full rounded-xl px-3 py-2.5 text-sm transition-all
                              border border-gray-200 bg-gray-50 
                              focus:bg-white focus:border-green-500 
                              focus:ring-2 focus:ring-green-100">
            </div>

            {{-- Guardian Phone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Guardian Phone
                </label>
                <input type="text" name="guardian_phone"
                       value="{{ old('guardian_phone', $student->guardian_phone) }}"
                       placeholder="e.g. 012 345 678"
                       class="w-full rounded-xl px-3 py-2.5 text-sm transition-all
                              border border-gray-200 bg-gray-50 
                              focus:bg-white focus:border-green-500 
                              focus:ring-2 focus:ring-green-100">
            </div>

            {{-- Relationship --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Relationship
                </label>
                <select name="guardian_relationship"
                        class="w-full rounded-xl px-3 py-2.5 text-sm transition-all appearance-none cursor-pointer
                               border border-gray-200 bg-gray-50 
                               focus:bg-white focus:border-green-500 
                               focus:ring-2 focus:ring-green-100">
                    <option value="">— Select —</option>
                    <option value="father" {{ old('guardian_relationship', $student->guardian_relationship) === 'father' ? 'selected' : '' }}>Father</option>
                    <option value="mother" {{ old('guardian_relationship', $student->guardian_relationship) === 'mother' ? 'selected' : '' }}>Mother</option>
                    <option value="other"  {{ old('guardian_relationship', $student->guardian_relationship) === 'other'  ? 'selected' : '' }}>Other</option>
                </select>
            </div>

        </div>
    </div>

    {{-- Actions --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm px-5 py-4 
                flex items-center justify-end gap-3">
        <a href="{{ route('teacher.students.show', $student) }}"
           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 
                  text-sm font-semibold rounded-xl transition-colors">
            Cancel
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 
                       hover:bg-green-700 text-white text-sm font-semibold 
                       rounded-xl transition-all shadow-sm hover:shadow-green-500/20 
                       active:scale-[0.98]">
            <i class="ti ti-device-floppy text-base"></i>
            Save Changes
        </button>
    </div>

</form>

@endsection