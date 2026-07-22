@php
    $layout = auth()->user()->isAdmin() ? 'layouts.admin' : 'layouts.teacher';
@endphp

@extends($layout, ['title' => 'គណនីរបស់ខ្ញុំ'])

@section('content')

<div class="max-w-7xl mx-auto pb-8" 
     x-data="{ 
        showPasswordModal: {{ $errors->has('password') ? 'true' : 'false' }} 
     }">

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
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-3">
            <i class="ti ti-user text-slate-600"></i>
            គណនីរបស់ខ្ញុំ
        </h1>

        <a href="{{ route('profile.edit') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 
                  bg-slate-800 hover:bg-slate-900 text-white 
                  text-sm font-semibold rounded-2xl transition-colors">
            <i class="ti ti-pencil text-base"></i>
            កែប្រែព័ត៌មាន
        </a>
    </div>

    {{-- Main Grid: 3 columns --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- ============================= --}}
        {{-- LEFT: Profile Card (1 col) --}}
        {{-- ============================= --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-6">

                {{-- Avatar --}}
                <div class="relative w-40 h-40 mx-auto mb-5">
                    @if($user->avatar)
                        <img src="{{ $user->avatarUrl() }}" 
                             alt="{{ $user->name }}"
                             class="w-40 h-40 rounded-full object-cover">
                    @else
                        <div class="w-40 h-40 rounded-full 
                                    bg-slate-100 text-slate-600 
                                    flex items-center justify-center 
                                    font-bold text-5xl">
                            {{ $user->initials() }}
                        </div>
                    @endif

                    {{-- Camera Button --}}
                    <form action="{{ route('profile.avatar') }}" method="POST" 
                          enctype="multipart/form-data" 
                          class="absolute bottom-1 right-1"
                          id="avatar-form">
                        @csrf
                        <label for="avatar-input" 
                               class="w-10 h-10 rounded-full bg-white 
                                      text-slate-600 flex items-center justify-center 
                                      cursor-pointer border-2 border-slate-200 
                                      hover:border-slate-800 hover:text-slate-800
                                      transition-colors">
                            <i class="ti ti-camera text-base"></i>
                        </label>
                        <input type="file" name="avatar" id="avatar-input" 
                               accept="image/*"
                               class="hidden"
                               onchange="document.getElementById('avatar-form').submit()">
                    </form>
                </div>

                {{-- Name with swap icon --}}
                <div class="flex items-center justify-center gap-2 mb-3">
                    <h2 class="text-xl font-bold text-slate-800">
                        {{ $user->name }}
                    </h2>
                    <i class="ti ti-arrows-exchange text-slate-400 text-lg"></i>
                </div>

                {{-- Role Badge --}}
                <div class="flex justify-center mb-4">
                    <span class="inline-flex items-center gap-1.5 px-4 py-1.5 
                                 rounded-full text-xs font-semibold 
                                 border border-slate-800 text-slate-800">
                        <i class="ti ti-id-badge text-sm"></i>
                        {{ $user->isAdmin() ? 'Administrator' : 'គ្រូបង្រៀន' }}
                    </span>
                </div>

                {{-- Username --}}
                <p class="text-center text-sm font-mono text-slate-500 mb-5">
                    {{ $user->username }}
                </p>

                {{-- Divider --}}
                <div class="border-t border-slate-200 mb-5"></div>

                {{-- Gender + DOB --}}
                <div class="flex items-center justify-center gap-6 text-sm text-slate-600">
                    <span class="flex items-center gap-1.5">
                        @if($user->gender === 'female')
    <span class="text-pink-500 font-bold text-base">♀</span>
@elseif($user->gender === 'male')
    <span class="text-blue-500 font-bold text-base">♂</span>
@else
    <span class="text-slate-400">—</span>
@endif
                        <span class="text-slate-400">-</span>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <i class="ti ti-calendar text-slate-400"></i>
                        {{ $user->date_of_birth?->format('Y-m-d') ?? '—' }}
                    </span>
                </div>

                {{-- QR Code Section --}}
                <div class="border-t border-slate-200 mt-6 pt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <i class="ti ti-qrcode text-slate-600 text-lg"></i>
                        <h3 class="text-sm font-bold text-slate-800">
                            កូដសម្គាល់ (QR Code)
                        </h3>
                    </div>

                    <div class="p-3 rounded-2xl border-2 border-slate-200 mb-4">
                        <div id="qr-container" class="flex items-center justify-center">
                            {!! QrCode::size(200)->generate($qrData) !!}
                        </div>
                    </div>

                    <button onclick="downloadQR()"
                            class="w-full inline-flex items-center justify-center gap-2 
                                   px-4 py-2.5 border-2 border-slate-800 
                                   text-slate-800 hover:bg-slate-800 hover:text-white
                                   text-sm font-semibold rounded-2xl transition-colors">
                        <i class="ti ti-download text-base"></i>
                        រក្សាទុក QR
                    </button>
                </div>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- MIDDLE: Info (2 cols) --}}
        {{-- ============================= --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Contact Info --}}
<div class="bg-white rounded-2xl border border-slate-200 p-6">
    <div class="flex items-center gap-2 mb-6 pb-4 border-b border-slate-200">
        <i class="ti ti-id text-slate-600 text-xl"></i>
        <h3 class="text-base font-bold text-slate-800">
            ព័ត៌មានទំនាក់ទំនង
        </h3>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
        {{-- Phone --}}
        <div class="flex items-start gap-3">
            <i class="ti ti-phone text-slate-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-xs text-slate-500 mb-1">លេខទូរស័ព្ទ</p>
                <p class="text-base font-bold text-slate-800">
                    {{ $user->phone ?? '—' }}
                </p>
            </div>
        </div>

        {{-- Nationality --}}
        <div class="flex items-start gap-3">
            <i class="ti ti-flag text-slate-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-xs text-slate-500 mb-1">សញ្ជាតិ | ជនជាតិ</p>
                <p class="text-base font-bold text-slate-800">
                    {{ $user->nationality ?? '—' }} | {{ $user->ethnicity ?? '—' }}
                </p>
            </div>
        </div>

        {{-- Birth Place --}}
        <div class="flex items-start gap-3 sm:col-span-2">
            <i class="ti ti-map-pin text-slate-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-xs text-slate-500 mb-1">ទីកន្លែងកំណើត</p>
                <p class="text-base font-bold text-slate-800">
                    {{ $user->birth_place ?? '—' }}
                </p>
            </div>
        </div>

        {{-- Current Address --}}
        <div class="flex items-start gap-3 sm:col-span-2">
            <i class="ti ti-map-pin text-slate-500 text-xl mt-0.5"></i>
            <div>
                <p class="text-xs text-slate-500 mb-1">អាសយដ្ឋានបច្ចុប្បន្ន</p>
                <p class="text-base font-bold text-slate-800">
                    {{ $user->current_address ?? '—' }}
                </p>
            </div>
        </div>
    </div>
</div>

            {{-- Teaching Info (Teachers Only) --}}
            @if($user->role === 'teacher' && $user->teacher)
                <div class="bg-white rounded-2xl border border-slate-200 p-6">
                    <div class="flex items-center gap-2 mb-6 pb-4 border-b border-slate-200">
                        <i class="ti ti-briefcase text-slate-600 text-xl"></i>
                        <h3 class="text-base font-bold text-slate-800">
                            ព័ត៌មានគ្រូបង្រៀន
                        </h3>
                    </div>

                    @if($user->teacher->classes->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($user->teacher->classes as $class)
                                {{-- School Card --}}
                                <div class="p-4 rounded-2xl border border-slate-200">
                                    <div class="flex items-start gap-3">
                                        <i class="ti ti-building text-slate-500 text-xl mt-1"></i>
                                        <div class="flex-1">
                                            <p class="text-xs text-slate-500 mb-1">គ្រឹះស្ថានសិក្សា</p>
                                            <p class="text-base font-bold text-slate-800 mb-2">
                                                {{ config('app.school_name', 'School Name') }}
                                            </p>
                                            <div class="flex flex-wrap gap-2">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 
                                                             rounded-lg text-xs font-semibold 
                                                             border border-slate-200 text-slate-600">
                                                    {{ $class->academicYear->name }}
                                                </span>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 
                                                             rounded-lg text-xs font-semibold 
                                                             border border-slate-200 text-slate-600">
                                                    {{ $class->session_period === 'morning' ? 'ព្រឹក' : 'រសៀល' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Class Card --}}
                                <div class="p-4 rounded-2xl border border-slate-200">
                                    <div class="flex items-start gap-3">
                                        <i class="ti ti-school text-slate-500 text-xl mt-1"></i>
                                        <div>
                                            <p class="text-xs text-slate-500 mb-1">ព័ត៌មានថ្នាក់សិក្សា</p>
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 
                                                         rounded-lg text-sm font-bold 
                                                         border border-slate-800 text-slate-800">
                                                {{ $class->name }} ({{ $class->grade->name }})
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-400 text-center py-4">
                            មិនទាន់មានថ្នាក់ណាត្រូវបានចាត់តាំង។
                        </p>
                    @endif
                </div>
            @endif

        </div>

        {{-- ============================= --}}
        {{-- RIGHT: Security (1 col) --}}
        {{-- ============================= --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Security --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-slate-200">
                    <i class="ti ti-shield-lock text-slate-600 text-xl"></i>
                    <h3 class="text-base font-bold text-slate-800">
                        សុវត្ថិភាពគណនី
                    </h3>
                </div>

                <div class="p-4 rounded-2xl border-2 border-slate-800 mb-4">
                    <div class="flex items-start gap-2 mb-3">
                        <i class="ti ti-lock text-slate-800 text-xl"></i>
                        <div>
                            <p class="text-sm font-bold text-slate-800">លេខសម្ងាត់</p>
                            <p class="text-xs text-slate-500 mt-1">
                                ផ្លាស់ប្តូរលេខសម្ងាត់ថ្មីជាទៀងទាត់
                            </p>
                        </div>
                    </div>

                    <button type="button"
        @click="showPasswordModal = true"
        class="w-full inline-flex items-center justify-center gap-2 
               px-4 py-3 bg-slate-800 hover:bg-slate-900 text-white
               text-sm font-bold rounded-xl transition-all 
               shadow-sm active:scale-[0.98]">
    <i class="ti ti-key text-base"></i>
    ប្តូរលេខសម្ងាត់
</button>
                </div>
            </div>

            {{-- Health & Wellness --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <div class="flex items-center gap-2 mb-6 pb-4 border-b border-slate-200">
                    <i class="ti ti-heartbeat text-slate-600 text-xl"></i>
                    <h3 class="text-base font-bold text-slate-800">
                        សុខភាព និងតម្រូវការ
                    </h3>
                </div>

                <div class="p-6 rounded-2xl border border-slate-200 text-center">
                    <i class="ti ti-heartbeat text-4xl text-slate-300 block mb-2"></i>
                    <p class="text-sm text-slate-400">មិនមានព័ត៌មានសុខភាព</p>
                </div>
            </div>

        </div>

    </div>

    {{-- Password Change Modal --}}
<div x-show="showPasswordModal" 
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     @click.self="showPasswordModal = false"
     @keydown.escape.window="showPasswordModal = false">
    
    <div x-show="showPasswordModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

        {{-- Modal Header --}}
        <div class="p-6 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="ti ti-lock text-red-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">ប្តូរលេខសម្ងាត់</h3>
                    <p class="text-xs text-slate-500">Change Password</p>
                </div>
            </div>
            <button @click="showPasswordModal = false"
                    class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center 
                           text-slate-400 hover:text-slate-800 transition-colors">
                <i class="ti ti-x"></i>
            </button>
        </div>

        {{-- Modal Form --}}
<form action="{{ route('profile.password') }}" method="POST" class="p-6 space-y-5"
      x-data="{ showPassword: false, showConfirm: false }">
    @csrf
    @method('PUT')

    {{-- New Password --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            លេខសម្ងាត់ថ្មី <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input :type="showPassword ? 'text' : 'password'" 
                   name="password" required
                   autocomplete="new-password"
                   class="w-full pl-4 pr-12 py-3 rounded-xl border border-slate-300 
                          focus:border-red-600 focus:ring-2 focus:ring-red-100
                          transition-colors text-sm
                          @error('password') border-red-400 @enderror">
            
            {{-- Toggle Button --}}
            <button type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 -translate-y-1/2 
                           w-8 h-8 rounded-lg flex items-center justify-center
                           text-slate-400 hover:text-slate-700 
                           hover:bg-slate-100 transition-colors">
                <i class="ti text-lg" 
                   :class="showPassword ? 'ti-eye-off' : 'ti-eye'"></i>
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Confirm Password --}}
    <div>
        <label class="block text-sm font-semibold text-slate-700 mb-2">
            បញ្ជាក់លេខសម្ងាត់ <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <input :type="showConfirm ? 'text' : 'password'" 
                   name="password_confirmation" required
                   autocomplete="new-password"
                   class="w-full pl-4 pr-12 py-3 rounded-xl border border-slate-300 
                          focus:border-red-600 focus:ring-2 focus:ring-red-100
                          transition-colors text-sm">
            
            {{-- Toggle Button --}}
            <button type="button"
                    @click="showConfirm = !showConfirm"
                    class="absolute right-3 top-1/2 -translate-y-1/2 
                           w-8 h-8 rounded-lg flex items-center justify-center
                           text-slate-400 hover:text-slate-700 
                           hover:bg-slate-100 transition-colors">
                <i class="ti text-lg" 
                   :class="showConfirm ? 'ti-eye-off' : 'ti-eye'"></i>
            </button>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-2">
        <i class="ti ti-info-circle text-amber-600 mt-0.5"></i>
        <p class="text-xs text-amber-800">
            លេខសម្ងាត់ត្រូវមានយ៉ាងតិច 8 តួអក្សរ
        </p>
    </div>

    {{-- Buttons --}}
    <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-200">
        <button type="button"
                @click="showPasswordModal = false"
                class="px-5 py-2.5 border border-slate-300 text-slate-700 
                       text-sm font-semibold rounded-xl hover:bg-slate-50 transition-colors">
            បោះបង់
        </button>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 
                       bg-red-600 hover:bg-red-700 text-white 
                       text-sm font-semibold rounded-xl transition-colors 
                       active:scale-[0.98]">
            <i class="ti ti-check"></i>
            រក្សាទុក
        </button>
    </div>
</form>
    </div>
</div>

</div>

@push('scripts')
<script>
function downloadQR() {
    const svg = document.querySelector('#qr-container svg');
    if (!svg) return;

    const svgData = new XMLSerializer().serializeToString(svg);
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    img.onload = function() {
        canvas.width = 500;
        canvas.height = 500;
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
        
        const link = document.createElement('a');
        link.download = 'qr-{{ $user->username }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    };
    
    img.src = 'data:image/svg+xml;base64,' + btoa(unescape(encodeURIComponent(svgData)));
}
</script>
@endpush

@endsection