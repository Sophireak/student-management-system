@extends('layouts.guest', ['title' => 'Login'])

@section('content')

<div class="mb-6">
    <h2 class="text-xl font-bold text-gray-800">Welcome back</h2>
    <p class="text-sm text-gray-500 mt-1">Sign in to your account to continue</p>
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
        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
        <div class="relative">
            <i class="ti ti-mail absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   placeholder="you@school.com"
                   class="w-full border rounded-lg pl-9 pr-3 py-2.5 text-sm
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                          {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
        </div>
        @error('email')
            <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                <i class="ti ti-alert-circle"></i> {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Password --}}
    <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <div class="relative">
            <i class="ti ti-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input type="password" name="password" id="password"
                   required autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full border rounded-lg pl-9 pr-10 py-2.5 text-sm
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500
                          {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}">
            <button type="button" onclick="togglePassword()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i class="ti ti-eye" id="eye-icon"></i>
            </button>
        </div>
        @error('password')
            <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                <i class="ti ti-alert-circle"></i> {{ $message }}
            </p>
        @enderror
    </div>

    {{-- Remember + Forgot --}}
    <div class="flex items-center justify-between mb-6">
        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
            <input type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
            Remember me
        </label>
        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               class="text-sm text-green-600 hover:text-green-700 hover:underline font-medium">
                Forgot password?
            </a>
        @endif
    </div>

    <button type="submit"
            class="w-full py-2.5 px-4 bg-green-600 hover:bg-green-700 text-white text-sm
                   font-semibold rounded-lg transition-colors focus:outline-none focus:ring-2
                   focus:ring-green-500 focus:ring-offset-2 flex items-center justify-center gap-2">
        <i class="ti ti-login text-base"></i>
        Sign In
    </button>

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
