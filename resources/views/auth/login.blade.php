@extends('layouts.guest', ['title' => 'Login'])

@section('content')

<div x-data="{ 
    showQrScanner: false,
    initScanner() {
        this.$nextTick(() => {
            setTimeout(() => startScanner(), 300);
        });
    }
}">

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Login</h2>
        <p class="text-sm text-gray-400 mt-1">Welcome back! Please login to your account.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
            {{ session('status') }}
        </div>
    @endif

    {{-- QR Login Button --}}
    <button type="button"
        @click="showQrScanner = true; setTimeout(() => window.startScanner(), 300);"
        class="w-full mb-4 py-3 px-4 bg-slate-800 hover:bg-slate-900 text-white text-sm
               font-semibold rounded-xl transition-colors focus:outline-none 
               flex items-center justify-center gap-2 shadow-lg shadow-slate-900/20">
    <i class="ti ti-qrcode text-lg"></i>
    Scan QR Code to Login
</button>

    {{-- Divider --}}
    <div class="flex items-center gap-3 my-5">
        <div class="flex-1 h-px bg-gray-200"></div>
        <span class="text-xs text-gray-400 font-medium">OR</span>
        <div class="flex-1 h-px bg-gray-200"></div>
    </div>

    <form method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        {{-- Username / Email --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">
                Username or Email
            </label>
            <input type="text" name="username" value="{{ old('username') }}"
                   required autocomplete="username"
                   placeholder="e.g. ADM10062000 or you@school.com"
                   class="w-full border rounded-lg px-3 py-2.5 text-sm bg-white
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                          {{ $errors->has('username') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
            @error('username')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                    <i class="ti ti-alert-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Your password</label>
            <div class="relative">
                <input type="password" name="password" id="password"
                       required autocomplete="current-password"
                       placeholder="••••••••"
                       class="w-full border rounded-lg px-3 py-2.5 pr-10 text-sm bg-white
                              focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                              {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
                <button type="button" onclick="togglePassword()"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <i class="ti ti-eye" id="eye-icon"></i>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1">
                    <i class="ti ti-alert-circle"></i> {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember --}}
        <div class="mb-5 mt-2">
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember"
                       class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                Keep me logged in
            </label>
        </div>

        <button type="submit"
                class="w-full py-3 px-4 bg-green-600 hover:bg-green-700 text-white text-sm
                       font-semibold rounded-full transition-colors focus:outline-none focus:ring-2
                       focus:ring-green-500 focus:ring-offset-2">
            Login
        </button>

        @if (Route::has('password.request'))
            <div class="text-center mt-4">
                <a href="{{ route('password.request') }}" 
                   class="text-xs text-gray-400 hover:text-green-600 hover:underline">
                    Forgot Password?
                </a>
            </div>
        @endif
    </form>

    {{-- ============================ --}}
    {{-- QR SCANNER MODAL --}}
    {{-- ============================ --}}
    <div x-show="showQrScanner" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         @click="showQrScanner = false; stopScanner();">
        
        <div x-show="showQrScanner"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

            {{-- Header --}}
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-slate-800 flex items-center justify-center">
                        <i class="ti ti-qrcode text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Scan QR Code</h3>
                        <p class="text-xs text-slate-500">Point camera at your QR code</p>
                    </div>
                </div>
                <button @click="showQrScanner = false; stopScanner();"
                        class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center 
                               text-slate-400 hover:text-slate-800 transition-colors">
                    <i class="ti ti-x"></i>
                </button>
            </div>

            {{-- Scanner --}}
            <div class="p-6">
                <div id="qr-reader" class="rounded-2xl overflow-hidden bg-slate-100 
                                            aspect-square flex items-center justify-center">
                    <div class="text-center text-slate-400">
                        <i class="ti ti-camera text-5xl mb-2"></i>
                        <p class="text-sm">Loading camera...</p>
                    </div>
                </div>

                <p id="qr-status" class="mt-4 text-sm text-center text-slate-600"></p>
            </div>

            {{-- Footer --}}
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    <i class="ti ti-info-circle"></i>
                    Allow camera access to scan
                </p>
                <button @click="showQrScanner = false; stopScanner();"
                        class="px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200 
                               rounded-lg transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
window.togglePassword = function() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eye-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('ti-eye', 'ti-eye-off');
    } else {
        input.type = 'password';
        icon.classList.replace('ti-eye-off', 'ti-eye');
    }
}

let html5QrCode = null;

window.startScanner = function() {
    const statusEl = document.getElementById('qr-status');
    const readerEl = document.getElementById('qr-reader');
    
    if (!window.Html5Qrcode) {
        statusEl.innerHTML = '<span class="text-red-600">QR library not loaded</span>';
        return;
    }

    if (html5QrCode) {
        console.log('Scanner already running');
        return;
    }

    readerEl.innerHTML = '';
    statusEl.innerHTML = '<span class="text-slate-500">Requesting camera...</span>';

    html5QrCode = new Html5Qrcode("qr-reader");

   html5QrCode.start(
    { facingMode: "environment" },
    { 
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0,
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        },
        showTorchButtonIfSupported: true,
        showZoomSliderIfSupported: true,
        defaultZoomValueIfSupported: 2,
    },
    (decodedText) => {
        console.log('QR detected:', decodedText);
        statusEl.innerHTML = '<span class="text-green-600">✓ QR detected! Logging in...</span>';
        window.stopScanner();
        setTimeout(() => window.location.href = decodedText, 500);
    },
    (errorMessage) => {}
).then(() => {
    console.log('Camera started');
    statusEl.innerHTML = '<span class="text-slate-600">Point camera at QR code</span>';
}).catch((err) => {
    console.error('Camera error:', err);
    statusEl.innerHTML = '<span class="text-red-600">Camera error: ' + err.toString() + '</span>';
    html5QrCode = null;
});
}

window.stopScanner = function() {
    if (html5QrCode) {
        html5QrCode.stop().then(() => {
            html5QrCode = null;
        }).catch(() => {
            html5QrCode = null;
        });
    }
}
</script>
@endpush

@endsection