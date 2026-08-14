<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Report') - {{ config('app.name') }}</title>

    {{-- Tabler Icons --}}
    <link rel="stylesheet" 
          href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    {{-- Khmer Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Moul&display=swap" rel="stylesheet">

    {{-- Vite --}}
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Battambang', 'Khmer OS', sans-serif;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .khmer-title {
            font-family: 'Moul', 'Khmer OS Muol', serif;
        }

        /* Print styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            
            .print-page {
                padding: 0 !important;
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            
            @page {
                margin: 1cm;
            }
            
            @page landscape {
                size: landscape;
                margin: 0.8cm;
            }
        }

        /* Landscape orientation for score list */
        @page score-list {
            size: landscape;
            margin: 0.8cm;
        }

        .landscape {
            page: score-list;
        }
    </style>

    @stack('styles')
</head>

<body class="bg-gray-100">

    {{-- Print Toolbar (Hidden when printing) --}}
    <div class="no-print bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2">
    {{-- Close button (works when opened in new tab) --}}
    <button onclick="window.close()"
            class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors">
        <i class="ti ti-x text-base"></i>
        Close
    </button>
</div>

            <div class="flex items-center gap-2">
                <button onclick="window.print()"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    <i class="ti ti-printer text-base"></i>
                    Print
                </button>
            </div>
        </div>
    </div>

    {{-- Report Content --}}
    <main class="max-w-5xl mx-auto p-6 print-page">
        <div class="bg-white shadow-sm border border-gray-200 rounded-lg p-8 print:shadow-none print:border-none print:p-0 print:rounded-none">
            @yield('content')
        </div>
    </main>

</body>
</html>