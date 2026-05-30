<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ config('app.name') }} — {{ $title ?? 'Login' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">

    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="text-center mb-8">
                <p class="text-4xl mb-3">🏫</p>
                <h1 class="text-2xl font-bold text-gray-800">
                    {{ config('app.name') }}
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Primary School Management System
                </p>
            </div>

            {{-- Card --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
                @yield('content')
            </div>

        </div>
    </div>

</body>
</html>