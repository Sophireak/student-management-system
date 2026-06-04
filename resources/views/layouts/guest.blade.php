<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name') }} – {{ $title ?? 'Login' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-cinematic {
            position: fixed; inset: 0; z-index: 0;
            background: linear-gradient(135deg, #064e3b 0%, #065f46 30%, #047857 60%, #059669 100%);
            overflow: hidden;
        }
        .ray {
            position: absolute; top: -20%; height: 140%;
            background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.06), transparent);
            transform-origin: top center;
            animation: sway linear infinite;
        }
        .ray:nth-child(1) { left:8%;  width:80px;  animation-duration:9s;  animation-delay:0s;   opacity:0.4; }
        .ray:nth-child(2) { left:20%; width:40px;  animation-duration:13s; animation-delay:-3s;  opacity:0.25; }
        .ray:nth-child(3) { left:33%; width:100px; animation-duration:11s; animation-delay:-6s;  opacity:0.3; }
        .ray:nth-child(4) { left:48%; width:60px;  animation-duration:15s; animation-delay:-2s;  opacity:0.2; }
        .ray:nth-child(5) { left:60%; width:90px;  animation-duration:10s; animation-delay:-8s;  opacity:0.35; }
        .ray:nth-child(6) { left:73%; width:50px;  animation-duration:14s; animation-delay:-4s;  opacity:0.2; }
        .ray:nth-child(7) { left:85%; width:70px;  animation-duration:12s; animation-delay:-1s;  opacity:0.3; }
        @keyframes sway { 0%{transform:rotate(-4deg)} 50%{transform:rotate(4deg)} 100%{transform:rotate(-4deg)} }

        .particle {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            animation: floatup linear infinite;
        }
        @keyframes floatup {
            0%   { transform:translateY(100vh) scale(0); opacity:0; }
            10%  { opacity:1; }
            90%  { opacity:1; }
            100% { transform:translateY(-10vh) scale(1); opacity:0; }
        }
        .p1  { width:6px; height:6px; left:10%; animation-duration:12s; animation-delay:0s; }
        .p2  { width:4px; height:4px; left:22%; animation-duration:18s; animation-delay:-4s; }
        .p3  { width:8px; height:8px; left:36%; animation-duration:14s; animation-delay:-8s; }
        .p4  { width:3px; height:3px; left:50%; animation-duration:20s; animation-delay:-2s; }
        .p5  { width:5px; height:5px; left:63%; animation-duration:16s; animation-delay:-6s; }
        .p6  { width:7px; height:7px; left:76%; animation-duration:11s; animation-delay:-10s; }
        .p7  { width:4px; height:4px; left:88%; animation-duration:19s; animation-delay:-3s; }
        .p8  { width:6px; height:6px; left:5%;  animation-duration:13s; animation-delay:-7s; }

        .orb { position:absolute; border-radius:50%; filter:blur(80px); animation:pulse ease-in-out infinite; }
        .orb-1 { width:500px; height:500px; background:rgba(16,185,129,0.2);  top:-100px; left:-100px; animation-duration:8s; }
        .orb-2 { width:400px; height:400px; background:rgba(5,150,105,0.15);  bottom:-80px; right:-80px; animation-duration:11s; animation-delay:-4s; }
        .orb-3 { width:300px; height:300px; background:rgba(52,211,153,0.1);  top:40%; left:40%; animation-duration:14s; animation-delay:-7s; }
        @keyframes pulse { 0%,100%{transform:scale(1); opacity:0.8} 50%{transform:scale(1.1); opacity:1} }

        .bg-overlay { position:fixed; inset:0; z-index:1; background:linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.3)); }
        .login-content { position:relative; z-index:10; }
    </style>
</head>
<body class="font-sans antialiased">

    <div class="bg-cinematic">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="ray"></div><div class="ray"></div><div class="ray"></div>
        <div class="ray"></div><div class="ray"></div><div class="ray"></div><div class="ray"></div>
        <div class="particle p1"></div><div class="particle p2"></div><div class="particle p3"></div>
        <div class="particle p4"></div><div class="particle p5"></div><div class="particle p6"></div>
        <div class="particle p7"></div><div class="particle p8"></div>
    </div>
    <div class="bg-overlay"></div>

    <div class="login-content min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">

            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-white border-opacity-30">
                    <i class="ti ti-school text-white text-3xl"></i>
                </div>
                <h1 class="text-2xl font-bold text-white">{{ config('app.name') }}</h1>
                <p class="text-green-200 text-sm mt-1">Primary School Management System</p>
            </div>

            <div class="bg-white bg-opacity-95 rounded-2xl border border-white border-opacity-50 shadow-2xl p-8">
                @yield('content')
            </div>

            <p class="text-center text-green-200 text-xs mt-6 opacity-70">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>

        </div>
    </div>

    @stack('scripts')
</body>
</html>
