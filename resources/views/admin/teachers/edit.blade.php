@extends('layouts.admin', ['title' => 'Edit Teacher'])

@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <a href="{{ route('admin.teachers.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors mb-4 group">
        <i class="ti ti-arrow-left text-base 
                  group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Teachers
    </a>
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br 
                    from-green-100 to-emerald-100 text-green-700 
                    flex items-center justify-center font-extrabold 
                    text-lg shadow-inner flex-shrink-0">
            {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                Edit Teacher
            </h1>
            <p class="text-sm text-gray-400 mt-0.5 flex items-center gap-1.5">
                <i class="ti ti-mail text-xs text-gray-300"></i>
                {{ $teacher->user->email }}
                @if($teacher->employee_id)
                    <span class="text-gray-300">·</span>
                    <i class="ti ti-id-badge text-xs text-gray-300"></i>
                    {{ $teacher->employee_id }}
                @endif
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

{{-- ✅ Main Update Form — clean, no nested forms --}}
<form method="POST" 
      action="{{ route('admin.teachers.update', $teacher) }}" 
      class="space-y-5">
    @csrf
    @method('PUT')

    {{-- Section 1: Account --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ti ti-lock text-blue-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Account Information</h2>
                <p class="text-xs text-gray-400">Login credentials for this teacher</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Full Name --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Full Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-user text-gray-400"></i>
                    </div>
                    <input type="text" name="name"
                           value="{{ old('name', $teacher->user->name) }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('name')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('name')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-mail text-gray-400"></i>
                    </div>
                    <input type="email" name="email"
                           value="{{ old('email', $teacher->user->email) }}"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('email')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('email')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="hidden sm:block"></div>

            {{-- New Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    New Password
                    <span class="text-xs font-normal text-gray-400 ml-1">
                        (leave blank to keep current)
                    </span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-lock text-gray-400"></i>
                    </div>
                    <input type="password" name="password"
                           placeholder="Enter new password"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('password')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('password')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Confirm New Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-lock-check text-gray-400"></i>
                    </div>
                    <input type="password" name="password_confirmation"
                           placeholder="Re-enter new password"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border border-gray-200 bg-gray-50 
                                  focus:bg-white focus:border-green-500 
                                  focus:ring-2 focus:ring-green-100">
                </div>
            </div>

        </div>
    </div>

    {{-- Section 2: Personal Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                <i class="ti ti-user-circle text-green-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-gray-700">Personal Information</h2>
                <p class="text-xs text-gray-400">Teacher's personal and contact details</p>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

            {{-- Employee ID --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Employee ID
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-id-badge text-gray-400"></i>
                    </div>
                    <input type="text" name="employee_id"
                           value="{{ old('employee_id', $teacher->employee_id) }}"
                           placeholder="e.g. EMP-001"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  @error('employee_id')
                                      border border-red-300 bg-red-50 
                                      focus:border-red-400 focus:ring-2 focus:ring-red-100
                                  @else
                                      border border-gray-200 bg-gray-50 
                                      focus:bg-white focus:border-green-500 
                                      focus:ring-2 focus:ring-green-100
                                  @enderror">
                </div>
                @error('employee_id')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Phone Number
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-phone text-gray-400"></i>
                    </div>
                    <input type="text" name="phone"
                           value="{{ old('phone', $teacher->phone) }}"
                           placeholder="e.g. 012 345 678"
                           class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all
                                  border border-gray-200 bg-gray-50 
                                  focus:bg-white focus:border-green-500 
                                  focus:ring-2 focus:ring-green-100">
                </div>
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Gender
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-gender-bigender text-gray-400"></i>
                    </div>
                    <select name="gender"
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm 
                                   transition-all appearance-none cursor-pointer
                                   @error('gender')
                                       border border-red-300 bg-red-50
                                   @else
                                       border border-gray-200 bg-gray-50 
                                       focus:bg-white focus:border-green-500 
                                       focus:ring-2 focus:ring-green-100
                                   @enderror">
                        <option value="">Select gender</option>
                        <option value="male"
                            {{ old('gender', $teacher->gender) === 'male' 
                                ? 'selected' : '' }}>
                            Male
                        </option>
                        <option value="female"
                            {{ old('gender', $teacher->gender) === 'female' 
                                ? 'selected' : '' }}>
                            Female
                        </option>
                    </select>
                </div>
                @error('gender')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="ti ti-alert-circle"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Date of Birth --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Date of Birth
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 
                                flex items-center pointer-events-none">
                        <i class="ti ti-calendar text-gray-400"></i>
                    </div>
                    <input type="date" name="date_of_birth"
                           value="{{ old('date_of_birth', $teacher->date_of_birth?->format('Y-m-d')) }}"
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

            {{-- Address --}}
            <div class="sm:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Address
                    <span class="text-xs font-normal text-gray-400 ml-1">(optional)</span>
                </label>
                <div class="relative">
                    <div class="absolute top-3 left-0 pl-3.5 
                                flex items-start pointer-events-none">
                        <i class="ti ti-map-pin text-gray-400"></i>
                    </div>
                    <textarea name="address" rows="2"
                              placeholder="Enter teacher's home address"
                              class="w-full rounded-xl pl-10 pr-4 py-2.5 
                                     text-sm transition-all resize-none
                                     border border-gray-200 bg-gray-50 
                                     focus:bg-white focus:border-green-500 
                                     focus:ring-2 focus:ring-green-100"
                    >{{ old('address', $teacher->address) }}</textarea>
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
            Last updated {{ $teacher->updated_at->diffForHumans() }}
        </p>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.teachers.show', $teacher) }}"
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
                Update Teacher
            </button>
        </div>
    </div>

</form>

{{-- =============================================
     BELOW THE MAIN FORM — All separate forms/actions
     ============================================= --}}
<div 
    x-data="{
        qrModal: false,
        deactivateModal: false
    }"
    class="space-y-4 mt-4"
>

    {{-- Security Actions --}}
    <div class="bg-white rounded-2xl border border-amber-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-amber-50 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center">
                <i class="ti ti-qrcode text-amber-500 text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-amber-700">Security Actions</h2>
                <p class="text-xs text-amber-400">Manage teacher's QR login access</p>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center 
                        sm:justify-between gap-4 
                        p-4 rounded-xl bg-amber-50/50 border border-amber-100">
                <div>
                    <p class="text-sm font-semibold text-gray-800">
                        Regenerate QR Login Code
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Old QR code will stop working. 
                        Teacher will need to use the new one.
                    </p>
                </div>
                {{-- ✅ Alpine modal instead of confirm() --}}
                <button
                    type="button"
                    @click="qrModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 
                           bg-white border border-amber-300 text-amber-700 
                           text-sm font-semibold rounded-xl 
                           hover:bg-amber-50 hover:border-amber-400 
                           transition-all active:scale-[0.98] whitespace-nowrap">
                    <i class="ti ti-refresh text-lg"></i>
                    Regenerate QR
                </button>
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
                <p class="text-xs text-red-400">
                    Irreversible actions for this teacher account
                </p>
            </div>
        </div>
        <div class="p-6">
            <div class="flex flex-col sm:flex-row sm:items-center 
                        sm:justify-between gap-4 
                        p-4 rounded-xl bg-red-50/50 border border-red-100">
                <div>
                    <p class="text-sm font-semibold text-gray-800">
                        Deactivate this teacher
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        This will remove their login access and unassign all classes.
                    </p>
                </div>
                {{-- ✅ Alpine modal instead of confirm() --}}
                <button
                    type="button"
                    @click="deactivateModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 
                           bg-white border border-red-200 text-red-600 
                           text-sm font-semibold rounded-xl 
                           hover:bg-red-50 hover:border-red-300 
                           transition-all active:scale-[0.98] whitespace-nowrap">
                    <i class="ti ti-user-off text-lg"></i>
                    Deactivate Account
                </button>
            </div>
        </div>
    </div>

    {{-- =============================================
         QR REGENERATE MODAL
         ============================================= --}}
    <div
        x-show="qrModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center 
               justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="qrModal = false"
        @keydown.escape.window="qrModal = false"
    >
        <div
            x-show="qrModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm"
        >
            <div class="w-12 h-12 rounded-2xl bg-amber-50 
                        flex items-center justify-center mx-auto mb-4">
                <i class="ti ti-qrcode text-amber-500 text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800 text-center mb-1">
                Regenerate QR Code?
            </h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                The old QR code for 
                <span class="font-semibold text-gray-700">
                    {{ $teacher->user->name }}
                </span>
                will stop working immediately.
            </p>
            <div class="flex gap-3">
                <button
                    @click="qrModal = false"
                    type="button"
                    class="flex-1 px-4 py-2.5 bg-gray-100 
                           hover:bg-gray-200 text-gray-600 
                           text-sm font-semibold rounded-xl 
                           transition-colors">
                    Cancel
                </button>
                <form
                    method="POST"
                    action="{{ route('admin.users.regenerate-qr', $teacher->user) }}"
                    class="flex-1"
                >
                    @csrf
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-amber-500 
                                   hover:bg-amber-600 text-white 
                                   text-sm font-semibold rounded-xl 
                                   transition-colors">
                        Yes, Regenerate
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- =============================================
         DEACTIVATE MODAL
         ============================================= --}}
    <div
        x-show="deactivateModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center 
               justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="deactivateModal = false"
        @keydown.escape.window="deactivateModal = false"
    >
        <div
            x-show="deactivateModal"
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
                <i class="ti ti-user-off text-red-500 text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800 text-center mb-1">
                Deactivate Teacher?
            </h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Are you sure you want to deactivate
                <span class="font-semibold text-gray-700">
                    {{ $teacher->user->name }}
                </span>?
                They will lose all access immediately.
            </p>
            <div class="flex gap-3">
                <button
                    @click="deactivateModal = false"
                    type="button"
                    class="flex-1 px-4 py-2.5 bg-gray-100 
                           hover:bg-gray-200 text-gray-600 
                           text-sm font-semibold rounded-xl 
                           transition-colors">
                    Cancel
                </button>
                <form
                    method="POST"
                    action="{{ route('admin.teachers.destroy', $teacher) }}"
                    class="flex-1"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-red-500 
                                   hover:bg-red-600 text-white 
                                   text-sm font-semibold rounded-xl 
                                   transition-colors">
                        Yes, Deactivate
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection