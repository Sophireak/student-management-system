@php
    $layout = auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.teacher';
@endphp

@extends($layout, ['title' => 'កែប្រែព័ត៌មានគណនី'])

@section('content')

<div class="max-w-7xl mx-auto pb-8">

    {{-- Success Message --}}
    @if (session('success'))
        <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-2xl 
                    flex items-center gap-3"
             x-data="{ show: true }" x-show="show" 
             x-init="setTimeout(() => show = false, 4000)">
            <i class="ti ti-check text-green-600 text-lg"></i>
            <p class="text-sm font-semibold text-green-700 flex-1">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6">
        <a href="{{ route('profile.show') }}"
           class="inline-flex items-center gap-1.5 text-sm text-slate-500 
                  hover:text-slate-800 transition-colors mb-2">
            <i class="ti ti-arrow-left"></i>
            ត្រឡប់ក្រោយ
        </a>
        <h1 class="text-2xl font-bold text-slate-800">
            កែប្រែព័ត៌មានគណនី
        </h1>
    </div>

    {{-- Personal Info Form --}}
    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
            
            {{-- Section Title --}}
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 rounded-xl border border-slate-200 
                            flex items-center justify-center">
                    <i class="ti ti-user text-slate-600 text-xl"></i>
                </div>
                <div class="border-b-2 border-slate-800 pb-1">
                    <h3 class="text-base font-bold text-slate-800">
                        ព័ត៌មានផ្ទាល់ខ្លួន
                    </h3>
                </div>
            </div>

            <div class="space-y-5">

                {{-- Row 1: Name (full width for single name field) --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        ឈ្មោះពេញ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" 
                           value="{{ old('name', $user->name) }}"
                           required
                           placeholder="ឧ. ព្រៀម ជំនាង"
                           class="w-full px-4 py-3 rounded-xl border border-slate-300 
                                  focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                                  transition-colors text-sm
                                  @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Row 2: Gender, Nationality, Ethnicity, Phone --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    {{-- Gender --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            ភេទ <span class="text-red-500">*</span>
                        </label>
                        <select name="gender"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 
                                       focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                                       transition-colors text-sm bg-white">
                            <option value="">-- ជ្រើសរើស --</option>
                            <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>ប្រុស</option>
                            <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>ស្រី</option>
                        </select>
                    </div>

                    {{-- Nationality --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            សញ្ជាតិ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nationality" 
                               value="{{ old('nationality', $user->nationality ?? 'ខ្មែរ') }}"
                               placeholder="ខ្មែរ"
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 
                                      focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                                      transition-colors text-sm">
                    </div>

                    {{-- Ethnicity --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            ជនជាតិ
                        </label>
                        <input type="text" name="ethnicity" 
                               value="{{ old('ethnicity', $user->ethnicity ?? 'ខ្មែរ') }}"
                               placeholder="ខ្មែរ"
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 
                                      focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                                      transition-colors text-sm">
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            លេខទូរស័ព្ទ
                        </label>
                        <input type="text" name="phone" 
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="012 345 678"
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 
                                      focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                                      transition-colors text-sm">
                    </div>
                </div>

                {{-- Row 3: Email + DOB --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            អ៊ីមែល <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" 
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 
                                      focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                                      transition-colors text-sm
                                      @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date of Birth --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            ថ្ងៃកំណើត (ថ្ងៃ/ខែ/ឆ្នាំ) <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" 
                               value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                               required
                               class="w-full px-4 py-3 rounded-xl border border-slate-300 
                                      focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                                      transition-colors text-sm">
                    </div>
                </div>

                {{-- Row 4: Username (Read-only) --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        ឈ្មោះអ្នកប្រើប្រាស់ <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none">
                            <i class="ti ti-user text-slate-400"></i>
                        </div>
                        <input type="text" 
                               value="{{ $user->username }}"
                               readonly
                               class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 
                                      bg-slate-50 text-slate-600 font-mono text-sm cursor-not-allowed">
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-1">
                        <i class="ti ti-info-circle"></i>
                        Login ID មិនអាចប្តូរបានទេ
                    </p>
                </div>
            </div>
        </div>

        {{-- Address Info (Admin Only) --}}
@if(auth()->user()->isAdmin())
    <div class="bg-white rounded-2xl border border-slate-200 p-6 mb-6">
        
        {{-- Section Title --}}
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 rounded-xl border border-slate-200 
                        flex items-center justify-center">
                <i class="ti ti-map-pin text-slate-600 text-xl"></i>
            </div>
            <div class="border-b-2 border-slate-800 pb-1">
                <h3 class="text-base font-bold text-slate-800">
                    អាសយដ្ឋាន
                </h3>
            </div>
        </div>

        <div class="space-y-5">
            {{-- Birth Place --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    ទីកន្លែងកំណើត
                </label>
                <input type="text" name="birth_place" 
                       value="{{ old('birth_place', $user->birth_place) }}"
                       placeholder="ភូមិ, ឃុំ, ស្រុក, ខេត្ត"
                       class="w-full px-4 py-3 rounded-xl border border-slate-300 
                              focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                              transition-colors text-sm">
            </div>

            {{-- Current Address --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">
                    អាសយដ្ឋានបច្ចុប្បន្ន
                </label>
                <textarea name="current_address" rows="3"
                          placeholder="ភូមិ, ឃុំ, ស្រុក, ខេត្ត"
                          class="w-full px-4 py-3 rounded-xl border border-slate-300 
                                 focus:border-slate-800 focus:ring-2 focus:ring-slate-100
                                 transition-colors text-sm">{{ old('current_address', $user->current_address) }}</textarea>
            </div>
        </div>
    </div>
@endif

        {{-- Save Button --}}
        <div class="flex items-center justify-end gap-3 mb-6">
            <a href="{{ route('profile.show') }}"
               class="px-6 py-3 border border-slate-300 text-slate-700 
                      text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                បោះបង់
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 
                           bg-slate-800 hover:bg-slate-900 text-white 
                           text-sm font-semibold rounded-xl transition-colors 
                           active:scale-[0.98] shadow-lg shadow-slate-900/20">
                <i class="ti ti-check text-base"></i>
                រក្សាទុកការកែប្រែ
            </button>
        </div>
    </form>


</div>

@endsection