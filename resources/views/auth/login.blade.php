@extends('layouts.guest', ['title' => 'Login'])

@section('content')

<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Login</h2>
    <p class="text-sm text-gray-400 mt-1">Welcome back! Please login to your account.</p>
</div>

@if (session('status'))
    <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-lg">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    {{-- Email --}}
    <div class="mb-4">
        <label class="block text-xs font-medium text-gray-500 mb-1.5">Your email</label>
        <input type="email" name="email" value="{{ old('email') }}"
               required autofocus autocomplete="username"
               placeholder="you@school.com"
               class="w-full border rounded-lg px-3 py-2.5 text-sm bg-white
                      focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                      {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-200' }}">
        @error('email')
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

    {{-- Social logins (currently disabled — uncomment + wire up Socialite if needed)
    <div class="text-center text-xs text-gray-400 my-4">or</div>
    <div class="flex gap-3 mb-5">
        <button type="button" class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-full py-2.5 text-sm text-gray-600 hover:bg-gray-50">
            <i class="ti ti-brand-facebook text-blue-600"></i> Facebook
        </button>
        <button type="button" class="flex-1 flex items-center justify-center gap-2 border border-gray-200 rounded-full py-2.5 text-sm text-gray-600 hover:bg-gray-50">
            <i class="ti ti-brand-google text-red-500"></i> Google
        </button>
    </div>
    --}}

    <div class="text-center text-sm text-gray-500 mt-5">
        Don't have an account?
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="text-green-600 font-medium hover:underline">Sign up</a>
        @endif
    </div>
    @if (Route::has('password.request'))
        <div class="text-center mt-2">
            <a href="{{ route('password.request') }}" class="text-xs text-gray-400 hover:text-green-600 hover:underline">
                Forgot Password?
            </a>
        </div>
    @endif

</form>

@push('scripts')
<script>
function togglePassword() {
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
</script>
@endpush

@endsection
