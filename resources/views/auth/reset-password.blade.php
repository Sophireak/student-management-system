@extends('layouts.guest', ['title' => 'Reset Password'])

@section('content')

<h2 class="text-lg font-bold text-gray-800 mb-6 text-center">
    Reset your password
</h2>

<form method="POST" action="{{ route('password.store') }}" novalidate>
    @csrf

    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Email Address
        </label>
        <input type="email"
               name="email"
               value="{{ old('email', $request->email) }}"
               required
               autofocus
               class="w-full border border-gray-300 rounded-md px-3 py-2
                      text-sm focus:outline-none focus:ring-2
                      focus:ring-blue-500
                      @error('email') border-red-400 @enderror">
        @error('email')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            New Password
        </label>
        <input type="password"
               name="password"
               required
               autocomplete="new-password"
               placeholder="Minimum 8 characters"
               class="w-full border border-gray-300 rounded-md px-3 py-2
                      text-sm focus:outline-none focus:ring-2
                      focus:ring-blue-500
                      @error('password') border-red-400 @enderror">
        @error('password')
            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">
            Confirm New Password
        </label>
        <input type="password"
               name="password_confirmation"
               required
               autocomplete="new-password"
               placeholder="Repeat password"
               class="w-full border border-gray-300 rounded-md px-3 py-2
                      text-sm focus:outline-none focus:ring-2
                      focus:ring-blue-500">
    </div>

    <button type="submit"
            class="w-full py-2 px-4 bg-blue-600 text-white text-sm
                   font-medium rounded-md hover:bg-blue-700">
        Reset Password
    </button>
</form>

@endsection