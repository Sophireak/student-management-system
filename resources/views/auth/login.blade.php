@extends('layouts.guest', ['title' => 'Login'])

@section('content')

<h2 class="text-lg font-bold text-gray-800 mb-6 text-center">
    Sign in to your account
</h2>

<form method="POST" action="{{ route('login') }}" novalidate>
    @csrf

    {{-- Email --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Email Address
        </label>
        <input type="email"
               name="email"
               value="{{ old('email') }}"
               required
               autofocus
               autocomplete="username"
               placeholder="you@school.com"
               class="w-full border border-gray-300 rounded-md px-3 py-2
                      text-sm focus:outline-none focus:ring-2
                      focus:ring-blue-500
                      @error('email') border-red-400 @enderror">
        @error('email')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Password --}}
    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Password
        </label>
        <input type="password"
               name="password"
               required
               autocomplete="current-password"
               placeholder="••••••••"
               class="w-full border border-gray-300 rounded-md px-3 py-2
                      text-sm focus:outline-none focus:ring-2
                      focus:ring-blue-500
                      @error('password') border-red-400 @enderror">
        @error('password')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    {{-- Remember me + forgot password --}}
    <div class="flex items-center justify-between mb-6">
        <label class="flex items-center gap-2 text-sm text-gray-600">
            <input type="checkbox"
                   name="remember"
                   class="w-4 h-4 text-blue-600 border-gray-300 rounded">
            Remember me
        </label>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}"
               class="text-sm text-blue-600 hover:underline">
                Forgot password?
            </a>
        @endif
    </div>

    <button type="submit"
            class="w-full py-2 px-4 bg-blue-600 text-white text-sm
                   font-medium rounded-md hover:bg-blue-700
                   focus:outline-none focus:ring-2 focus:ring-blue-500">
        Sign In
    </button>
</form>

@endsection