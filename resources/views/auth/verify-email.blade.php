@extends('layouts.guest', ['title' => 'Verify Email'])

@section('content')

<h2 class="text-lg font-bold text-gray-800 mb-2 text-center">
    Verify your email
</h2>
<p class="text-sm text-gray-500 text-center mb-6">
    Please verify your email address by clicking the link we sent you.
</p>

@if (session('status') == 'verification-link-sent')
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200
                text-green-800 rounded-md text-sm">
        A new verification link has been sent to your email.
    </div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button type="submit"
            class="w-full py-2 px-4 bg-blue-600 text-white text-sm
                   font-medium rounded-md hover:bg-blue-700 mb-4">
        Resend Verification Email
    </button>
</form>

<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit"
            class="w-full py-2 px-4 bg-gray-100 text-gray-700 text-sm
                   font-medium rounded-md hover:bg-gray-200">
        Log Out
    </button>
</form>

@endsection