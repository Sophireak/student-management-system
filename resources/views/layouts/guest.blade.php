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
        * { box-sizing: border-box; }

        body { margin: 0; font-family: 'Segoe UI', sans-serif; }

        /* Aurora background */
        .bg-aurora {
    position: fixed; inset: 0; z-index: 0;
    background-color: #0d1b3e;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='115' viewBox='0 0 100 115'%3E%3Cpolygon points='50 0, 100 28.75, 100 86.25, 50 115, 0 86.25, 0 28.75' fill='none' stroke='rgba(100,140,255,0.12)' stroke-width='1'/%3E%3C/svg%3E");
    background-size: 80px 92px;
    overflow: hidden;
}
        .aurora-layer {
            position: absolute;
            width: 200%; height: 200%;
            top: -50%; left: -50%;
            border-radius: 50%;
            filter: blur(90px);
            animation: auroraFloat ease-in-out infinite alternate;
        }
        .aurora-1 { background: radial-gradient(ellipse, rgba(0,255,180,0.35), transparent 60%); animation-duration:10s; transform-origin:60% 40%; }
        .aurora-2 { background: radial-gradient(ellipse, rgba(100,60,255,0.3),  transparent 60%); animation-duration:14s; animation-delay:-4s;  transform-origin:30% 70%; }
        .aurora-3 { background: radial-gradient(ellipse, rgba(0,180,255,0.25),  transparent 60%); animation-duration:18s; animation-delay:-8s;  transform-origin:70% 55%; }
        .aurora-4 { background: radial-gradient(ellipse, rgba(180,0,255,0.2),   transparent 60%); animation-duration:22s; animation-delay:-12s; transform-origin:40% 30%; }
        @keyframes auroraFloat {
            0%   { transform: rotate(0deg)  scale(1); }
            33%  { transform: rotate(8deg)  scale(1.1); }
            66%  { transform: rotate(-6deg) scale(0.95); }
            100% { transform: rotate(4deg)  scale(1.05); }
        }

        .stars {
            position: absolute; inset: 0;
            background-image:
                radial-gradient(1px 1px at 15% 25%, white, transparent),
                radial-gradient(1px 1px at 40% 10%, white, transparent),
                radial-gradient(1.5px 1.5px at 70% 15%, white, transparent),
                radial-gradient(1px 1px at 85% 40%, white, transparent),
                radial-gradient(1px 1px at 25% 60%, white, transparent),
                radial-gradient(1px 1px at 55% 75%, white, transparent),
                radial-gradient(1.5px 1.5px at 90% 80%, white, transparent),
                radial-gradient(1px 1px at 10% 90%, white, transparent);
            animation: twinkle 4s ease-in-out infinite alternate;
            opacity: 0.6;
        }
        @keyframes twinkle { 0%{opacity:0.3} 100%{opacity:0.8} }

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
        .p1 { width:6px; height:6px; left:10%; animation-duration:12s; animation-delay:0s; }
        .p2 { width:4px; height:4px; left:22%; animation-duration:18s; animation-delay:-4s; }
        .p3 { width:8px; height:8px; left:36%; animation-duration:14s; animation-delay:-8s; }
        .p4 { width:3px; height:3px; left:50%; animation-duration:20s; animation-delay:-2s; }
        .p5 { width:5px; height:5px; left:63%; animation-duration:16s; animation-delay:-6s; }
        .p6 { width:7px; height:7px; left:76%; animation-duration:11s; animation-delay:-10s; }

        /* Page wrapper */
        .page-wrap {
            position: relative; z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Split card */
        .split-card {
            display: flex;
            width: 100%;
            max-width: 900px;
            min-height: 560px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.5), 0 0 60px rgba(0,255,180,0.08);
            animation: cardIn 0.8s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes cardIn {
            from { opacity:0; transform:translateY(40px) scale(0.97); }
            to   { opacity:1; transform:translateY(0) scale(1); }
        }

        /* Left panel */
        .left-panel {
            flex: 1;
            background: linear-gradient(145deg, #0f1f4a 0%, #1a1060 50%, #0d2040 100%);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Left panel inner glow */
        .left-panel::before {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(0,255,180,0.12), transparent 70%);
            top: -60px; left: -60px;
            border-radius: 50%;
            animation: pulse 6s ease-in-out infinite;
        }
        .left-panel::after {
            content: '';
            position: absolute;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(100,60,255,0.15), transparent 70%);
            bottom: -40px; right: -40px;
            border-radius: 50%;
            animation: pulse 8s ease-in-out infinite reverse;
        }
        @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.15)} }

        /* Logo icon */
        .logo-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, rgba(0,255,180,0.2), rgba(100,60,255,0.2));
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
            position: relative;
            z-index: 1;
            animation: iconPulse 3s ease-in-out infinite;
        }
        @keyframes iconPulse {
            0%,100% { box-shadow: 0 0 20px rgba(0,255,180,0.2); }
            50%      { box-shadow: 0 0 40px rgba(0,255,180,0.5), 0 0 60px rgba(100,60,255,0.2); }
        }

        .left-panel h2 {
            color: #fff;
            font-size: 26px;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 12px;
            position: relative; z-index: 1;
        }
        .left-panel h2 span {
            background: linear-gradient(90deg, #00ffb4, #7b6fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .left-panel p {
            color: rgba(255,255,255,0.55);
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 40px;
            position: relative; z-index: 1;
        }

        /* Feature icons */
        .features {
            display: flex;
            gap: 28px;
            position: relative; z-index: 1;
        }
        .feature {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.6);
            font-size: 12px;
        }
        .feature-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #00ffb4;
            transition: all 0.3s;
        }
        .feature:hover .feature-icon {
            background: rgba(0,255,180,0.1);
            border-color: rgba(0,255,180,0.3);
            transform: translateY(-3px);
        }

        /* Right panel */
        .right-panel {
            flex: 1;
            background: #fff;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        /* Footer */
        .page-footer {
            position: relative; z-index: 10;
            text-align: center;
            color: rgba(255,255,255,0.4);
            font-size: 12px;
            margin-top: 20px;
        }
    </style>
</head>
<body class="font-sans antialiased">

    {{-- Aurora background --}}
    <div class="bg-aurora">
        <div style="position:absolute;inset:0;background:radial-gradient(ellipse at 30% 50%, rgba(30,60,180,0.4), transparent 60%),radial-gradient(ellipse at 70% 50%, rgba(10,30,100,0.5), transparent 60%);"></div>
        <div class="particle p1"></div><div class="particle p2"></div>
        <div class="particle p3"></div><div class="particle p4"></div>
        <div class="particle p5"></div><div class="particle p6"></div>
    </div>

    <div class="page-wrap">
        <div class="w-full" style="max-width:900px;">

            <div class="split-card">

                {{-- Left panel --}}
                <div class="left-panel">
                    <div class="logo-icon">
                        <i class="ti ti-school text-white text-3xl"></i>
                    </div>

                    <h2>Empowering schools.<br><span>Inspiring futures.</span></h2>
                    <p>Streamline administration, enhance communication, and focus on what matters most — <span style="color:#00ffb4;">your students.</span></p>

                    <div class="features">
                        <div class="feature">
                            <div class="feature-icon"><i class="ti ti-shield-check"></i></div>
                            <span>Secure</span>
                        </div>
                        <div class="feature">
                            <div class="feature-icon"><i class="ti ti-bolt"></i></div>
                            <span>Fast</span>
                        </div>
                        <div class="feature">
                            <div class="feature-icon"><i class="ti ti-chart-bar"></i></div>
                            <span>Reliable</span>
                        </div>
                    </div>
                </div>

                {{-- Right panel --}}
                <div class="right-panel">
                    @yield('content')
                </div>

            </div>

            <div class="page-footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>

        </div>
    </div>

    @stack('scripts')
</body>
</html>