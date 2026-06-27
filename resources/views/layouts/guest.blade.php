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

        /* Animated gradient background with floating blobs */
        .bg-split {
            position: fixed; inset: 0; z-index: 0;
            background: linear-gradient(135deg, #1e293b 0%, #166534 45%, #22c55e 100%);
            overflow: hidden;
        }
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.55;
        }
        .blob-1 {
            width: 420px; height: 420px;
            background: #22c55e;
            top: -120px; left: -100px;
            animation: float1 12s ease-in-out infinite;
        }
        .blob-2 {
            width: 360px; height: 360px;
            background: #4ade80;
            bottom: -140px; right: -100px;
            animation: float2 14s ease-in-out infinite;
        }
        .blob-3 {
            width: 260px; height: 260px;
            background: #86efac;
            top: 40%; left: 60%;
            animation: float1 16s ease-in-out infinite reverse;
        }
        @keyframes float1 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(30px, 40px) scale(1.08); }
        }
        @keyframes float2 {
            0%, 100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-25px, -30px) scale(1.05); }
        }

        .page-wrap {
            position: relative; z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .login-card {
            display: flex;
            width: 100%;
            max-width: 960px;
            min-height: 520px;
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px);
            border-radius: 32px;
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0,0,0,0.35);
            border: 1px solid rgba(255,255,255,0.4);
        }
        .illus-panel {
            flex: 1.1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            background: linear-gradient(160deg, #ecfdf5, #d1fae5);
        }
        .brand {
            position: absolute;
            top: 28px; left: 32px; right: 32px;
            display: flex; align-items: flex-start; gap: 12px;
        }
        .brand-icon {
            width: 40px; height: 40px;
            flex-shrink: 0;
            background: linear-gradient(135deg, #22c55e, #15803d);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 14px rgba(22,163,74,0.35);
        }
        .brand-icon i { color: #fff; font-size: 19px; }
        .brand-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        .brand-text .brand-title {
            font-weight: 800;
            font-size: 14.5px;
            line-height: 1.45;
            color: #0f172a;
        }
        .brand-text .brand-subtitle {
            font-size: 11.5px;
            font-weight: 600;
            color: #16a34a;
            letter-spacing: 0.03em;
        }
        .form-panel {
            flex: 1;
            padding: 56px 48px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        .lang-badge {
            position: absolute;
            top: 20px; right: 20px;
            cursor: pointer;
            z-index: 20;
        }
        .lang-pill {
            display: flex; align-items: center; gap: 5px;
            font-size: 13px; font-weight: 700;
            padding: 6px 14px;
            border-radius: 999px;
            border: 1px solid;
            transition: all 0.15s;
        }
        .lang-pill.is-en {
            color: #2563eb;
            border-color: #bfdbfe;
            background: #eff6ff;
        }
        .lang-pill.is-km {
            color: #dc2626;
            border-color: #fecaca;
            background: #fef2f2;
        }
        .lang-menu {
            display: none;
            position: absolute;
            top: 38px; right: 0;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            padding: 6px;
            min-width: 120px;
            z-index: 20;
        }
        .lang-menu.open { display: block; }
        .lang-menu div {
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            display: flex; align-items: center; gap: 6px;
        }
        .lang-menu div:hover { background: #f1f5f9; }
        .lang-menu .opt-en { color: #2563eb; }
        .lang-menu .opt-km { color: #dc2626; }
        .mobile-brand {
            display: none;
        }
        .mobile-brand-divider {
            display: none;
        }
        .page-footer {
            position: relative; z-index: 10;
            text-align: center;
            color: rgba(255,255,255,0.65);
            font-size: 12px;
            font-weight: 500;
            margin-top: 18px;
        }

        @media (max-width: 760px) {
            .page-wrap { padding: 14px; }
            .login-card {
                flex-direction: column;
                min-height: 0;
                max-width: 420px;
                border-radius: 28px;
                box-shadow: 0 24px 60px rgba(0,0,0,0.4);
            }
            .illus-panel { display: none; }
            .form-panel { padding: 52px 26px 32px; }
            .lang-badge { top: 16px; right: 16px; }
            .lang-pill { font-size: 12px; padding: 5px 12px; }

            .mobile-brand {
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 14px;
                margin-bottom: 8px;
                padding-right: 0;
            }
            .mobile-brand-icon {
                width: 64px; height: 64px;
                flex-shrink: 0;
                background: linear-gradient(135deg, #4ade80, #16a34a, #14532d);
                border-radius: 20px;
                display: flex; align-items: center; justify-content: center;
                box-shadow: 0 10px 24px rgba(22,163,74,0.45), 0 0 0 6px rgba(34,197,94,0.12);
                transform: rotate(-4deg);
                transition: transform 0.3s;
            }
            .mobile-brand-icon i { color: #fff; font-size: 30px; }
            .mobile-brand-text {
                display: flex;
                flex-direction: column;
                gap: 6px;
                align-items: center;
            }
            .mobile-brand-title {
                font-weight: 800;
                font-size: 14.5px;
                line-height: 1.65;
                color: #0f172a;
                max-width: 280px;
            }
            .mobile-brand-subtitle {
                font-size: 11px;
                font-weight: 700;
                color: #16a34a;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                background: linear-gradient(90deg, #16a34a, #22c55e);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .mobile-brand-divider {
                display: block;
                width: 56px;
                height: 4px;
                background: linear-gradient(90deg, #22c55e, #15803d);
                border-radius: 999px;
                margin: 18px auto 26px;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="bg-split">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>
    <div class="page-wrap">
        <div class="w-full" style="max-width:960px;">
            <div class="login-card">
                {{-- Illustration panel --}}
                <div class="illus-panel">
                    <div class="brand">
                        <div class="brand-icon"><i class="ti ti-school"></i></div>
                        <div class="brand-text">
                            <span class="brand-title">ប្រព័ន្ធគ្រប់គ្រងសិស្សសាលាបឋមសិក្សា សម្ដេច ជា ស៊ីម</span>
                            <span class="brand-subtitle">School Management System</span>
                        </div>
                    </div>
                    <svg viewBox="0 0 320 300" width="100%" style="max-width:300px;">
                        {{-- main card --}}
                        <rect x="60" y="40" width="170" height="190" rx="14" fill="#eaf3de"/>
                        <rect x="78" y="62" width="100" height="10" rx="5" fill="#bcd9a4"/>
                        <rect x="78" y="84" width="134" height="10" rx="5" fill="#bcd9a4"/>
                        <rect x="78" y="106" width="114" height="10" rx="5" fill="#bcd9a4"/>
                        <rect x="78" y="148" width="80" height="26" rx="13" fill="#16a34a"/>
                        {{-- person sitting at the card --}}
                        <circle cx="145" cy="230" r="20" fill="#fcd9a8"/>
                        <path d="M112 286 Q145 258 178 286 L172 300 L118 300 Z" fill="#0f172a"/>
                        <rect x="132" y="246" width="26" height="18" fill="#fcd9a8"/>
                        {{-- gear icon, anchored top-right of card --}}
                        <circle cx="232" cy="58" r="24" fill="#fff" stroke="#16a34a" stroke-width="2"/>
                        <circle cx="232" cy="58" r="8" fill="#16a34a"/>
                        <g stroke="#16a34a" stroke-width="3">
                            <line x1="232" y1="38" x2="232" y2="44"/>
                            <line x1="232" y1="72" x2="232" y2="78"/>
                            <line x1="212" y1="58" x2="218" y2="58"/>
                            <line x1="246" y1="58" x2="252" y2="58"/>
                        </g>
                        {{-- lock icon, anchored bottom-right of card --}}
                        <circle cx="248" cy="196" r="26" fill="#fff" stroke="#16a34a" stroke-width="2"/>
                        <rect x="236" y="194" width="24" height="18" rx="3" fill="#16a34a"/>
                        <path d="M240 194 v-6 a8 8 0 0 1 16 0 v6" fill="none" stroke="#16a34a" stroke-width="3"/>
                        {{-- small plant, bottom-left of card --}}
                        <rect x="38" y="252" width="22" height="20" rx="3" fill="#cfe8b8"/>
                        <path d="M49 252 Q38 226 49 204 Q60 226 49 252 Z" fill="#16a34a"/>
                        <path d="M49 252 Q59 230 72 222 Q67 244 49 252 Z" fill="#3fae5e"/>
                    </svg>
                </div>
                {{-- Form panel --}}
                <div class="form-panel">
                    <div class="lang-badge">
                        <div class="lang-pill is-en" id="lang-pill" onclick="document.getElementById('lang-menu').classList.toggle('open')">
                            <i class="ti ti-world"></i> <span id="lang-current-text">EN</span>
                        </div>
                        <div id="lang-menu" class="lang-menu">
                            <div class="opt-en" onclick="setLang('en')"><i class="ti ti-flag"></i> English</div>
                            <div class="opt-km" onclick="setLang('km')"><i class="ti ti-flag"></i> ខ្មែរ</div>
                        </div>
                    </div>
                    <div class="mobile-brand">
                        <div class="mobile-brand-icon"><i class="ti ti-school"></i></div>
                        <div class="mobile-brand-text">
                            <span class="mobile-brand-title">ប្រព័ន្ធគ្រប់គ្រងសិស្សសាលាបឋមសិក្សា សម្ដេច ជា ស៊ីម</span>
                            <span class="mobile-brand-subtitle">School Management System</span>
                        </div>
                    </div>
                    <div class="mobile-brand-divider"></div>
                    @yield('content')
                </div>
            </div>
            <div class="page-footer">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
        </div>
    </div>
    @stack('scripts')
    {{-- Google Translate widget (hidden UI, triggered via dropdown) --}}
    <div id="google_translate_element" style="display:none;"></div>
    <script type="text/javascript">
        function googleTranslateElementInit() {
            new google.translate.TranslateElement(
                { pageLanguage: 'en', includedLanguages: 'en,km', autoDisplay: false },
                'google_translate_element'
            );
        }
        function setLang(lang) {
            const value = lang === 'en' ? '/en/en' : '/en/km';
            document.cookie = 'googtrans=' + value + '; path=/';
            document.cookie = 'googtrans=' + value + '; path=/; domain=' + window.location.hostname;
            window.location.reload();
        }
        // Reflect current language on the pill (color + label)
        (function initLangPill() {
            const match = document.cookie.match(/googtrans=\/en\/(en|km)/);
            const current = match ? match[1] : 'en';
            const pill = document.getElementById('lang-pill');
            const text = document.getElementById('lang-current-text');
            if (!pill || !text) return;
            if (current === 'km') {
                pill.classList.remove('is-en');
                pill.classList.add('is-km');
                text.textContent = 'ខ្មែរ';
            } else {
                pill.classList.remove('is-km');
                pill.classList.add('is-en');
                text.textContent = 'EN';
            }
        })();
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>
