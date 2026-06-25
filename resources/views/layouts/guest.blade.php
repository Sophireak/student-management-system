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

        .bg-split {
            position: fixed; inset: 0; z-index: 0;
            background: #eef1f8;
            overflow: hidden;
        }
        .bg-split::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 60%; height: 100%;
            background: #dfe6f5;
            clip-path: polygon(0 0, 70% 0, 40% 100%, 0 100%);
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
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(30,40,80,0.12);
        }

        .illus-panel {
            flex: 1.1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .brand {
            position: absolute;
            top: 36px; left: 40px;
            display: flex; align-items: center; gap: 8px;
            font-weight: 800; font-size: 18px;
            color: #0f172a;
        }
        .brand i { color: #16a34a; font-size: 22px; }

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
            font-size: 13px; font-weight: 600;
            padding: 6px 12px;
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
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            padding: 6px;
            min-width: 120px;
            z-index: 20;
        }
        .lang-menu.open { display: block; }
        .lang-menu div {
            padding: 8px 10px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            display: flex; align-items: center; gap: 6px;
        }
        .lang-menu div:hover { background: #f1f5f9; }
        .lang-menu .opt-en { color: #2563eb; }
        .lang-menu .opt-km { color: #dc2626; }

        .page-footer {
            position: relative; z-index: 10;
            text-align: center;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 18px;
        }

        @media (max-width: 760px) {
            .page-wrap { padding: 14px; }
            .login-card {
                flex-direction: column;
                min-height: 0;
                max-width: 420px;
                border-radius: 22px;
            }
            .illus-panel { display: none; }
            .form-panel { padding: 56px 26px 32px; }
            .lang-badge { top: 14px; right: 14px; }
            .lang-pill { font-size: 12px; padding: 5px 10px; }
        }
    </style>
</head>
<body class="font-sans antialiased">

    <div class="bg-split"></div>

    <div class="page-wrap">
        <div class="w-full" style="max-width:960px;">

            <div class="login-card">

                {{-- Illustration panel --}}
                <div class="illus-panel">
                    <div class="brand"><i class="ti ti-school"></i> School Portal</div>

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
