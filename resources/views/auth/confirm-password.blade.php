@extends('layouts.guest', ['title' => 'Confirm Password'])

@section('content')

<h2 class="text-lg font-bold text-gray-800 mb-2 text-center">
    Confirm your password
</h2>
<p class="text-sm text-gray-500 text-center mb-6">
    This is a secure area. Please confirm your password to continue.
</p>

<form method="POST" action="{{ route('password.confirm') }}" novalidate>
    @csrf

    <div class="mb-6">
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

    <button type="submit"
            class="w-full py-2 px-4 bg-blue-600 text-white text-sm
                   font-medium rounded-md hover:bg-blue-700">
        Confirm Password
    </button>
</form>

@endsection