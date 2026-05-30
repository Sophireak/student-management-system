@extends('layouts.guest', ['title' => 'Forgot Password'])

@section('content')

<h2 class="text-lg font-bold text-gray-800 mb-2 text-center">
    Forgot your password?
</h2>
<p class="text-sm text-gray-500 text-center mb-6">
    Enter your email and we'll send you a reset link.
</p>

@if (session('status'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200
                text-green-800 rounded-md text-sm">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" novalidate>
    @csrf

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Email Address
        </label>
        <input type="email"
               name="email"
               value="{{ old('email') }}"
               required
               autofocus
               placeholder="you@school.com"
               class="w-full border border-gray-300 rounded-md px-3 py-2
                      text-sm focus:outline-none focus:ring-2
                      focus:ring-blue-500
                      @error('email') border-red-400 @enderror">
        @error('email')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <button type="submit"
            class="w-full py-2 px-4 bg-blue-600 text-white text-sm
                   font-medium rounded-md hover:bg-blue-700">
        Send Reset Link
    </button>

    <div class="mt-4 text-center">
        <a href="{{ route('login') }}"
           class="text-sm text-gray-500 hover:text-gray-700">
            ← Back to login
        </a>
    </div>
</form>

@endsection