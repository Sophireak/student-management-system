@php use App\Helpers\KhmerDate; @endphp

<style>
    @page { 
        size: A4 portrait; 
        margin: 1cm; 
    }

    .honor-page {
        max-height: 27cm;
        overflow: hidden;
    }

    @media print {
        .honor-page {
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        
        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        .no-print, .no-print * {
            display: none !important;
        }
    }
</style>

<div class="honor-page">

    {{-- Cambodia National Header --}}
    <div class="text-center mb-2">
        <h2 class="khmer-title text-base font-bold">ព្រះរាជាណាចក្រកម្ពុជា</h2>
        <p class="text-xs">ជាតិ សាសនា ព្រះមហាក្សត្រ</p>
        <div class="flex items-center justify-center gap-2 my-1">
            <div class="w-12 border-b border-gray-800"></div>
            <div class="text-gray-800 text-xs">✦</div>
            <div class="w-12 border-b border-gray-800"></div>
        </div>
    </div>

    {{-- School Info --}}
    <div class="mb-2 text-xs">
        <p>{{ config('app.school_name', 'ការិយាល័យអប់រំ យុវជន និងកីឡា ក្រុងបាត់ដំបង') }}</p>
        <p>បឋមសិក្សា · {{ $class->name }}</p>
    </div>

    {{-- Title --}}
    <div class="text-center mb-3">
        <h1 class="khmer-title text-xl font-bold text-gray-800">
            តារាងកិត្តិយស {{ $periodLabel }}
        </h1>
        <p class="text-xs mt-0.5">ឆ្នាំសិក្សា {{ $academicYear->name }}</p>
    </div>

    {{-- Top 5 Students --}}
    @php
        $top5 = $enrollments
            ->sortBy(function ($e) use ($summary) {
                return $summary[$e->id]['rank'] ?? 999;
            })
            ->filter(function ($e) use ($summary) {
                return isset($summary[$e->id]['rank']) && $summary[$e->id]['rank'] <= 5;
            })
            ->values();
    @endphp

    @if ($top5->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-400 text-sm">មិនមានទិន្នន័យ</p>
        </div>
    @else
    <div class="space-y-3 my-3">

        {{-- 1st Place (Top - Larger) --}}
        @if (isset($top5[0]))
            <div class="flex justify-center">
                <div class="text-center">
                    <p class="text-blue-700 font-bold text-base underline mb-1">1</p>
                    <div class="w-24 h-32 border-4 border-pink-400 mx-auto mb-1 rounded-sm overflow-hidden">
                        @if ($top5[0]->student->photo)
                            <img src="{{ asset('storage/' . $top5[0]->student->photo) }}" 
                                 alt="{{ $top5[0]->student->full_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center 
                                        bg-gradient-to-br {{ strtolower($top5[0]->student->gender ?? '') === 'female' 
                                            ? 'from-pink-100 to-rose-100 text-pink-700' 
                                            : 'from-blue-100 to-indigo-100 text-blue-700' }} 
                                        text-3xl font-bold">
                                {{ strtoupper(substr($top5[0]->student->first_name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="w-24 border-b-2 border-blue-400 mx-auto mb-1"></div>
                    <p class="text-xs font-bold">{{ $top5[0]->student->full_name }}</p>
                </div>
            </div>
        @endif

        {{-- 2nd & 3rd (Side by side) --}}
        <div class="flex justify-around gap-4">
            @foreach ([1, 2] as $index)
                @if (isset($top5[$index]))
                    <div class="text-center">
                        <p class="text-blue-700 font-bold text-base underline mb-1">{{ $index + 1 }}</p>
                        <div class="w-20 h-28 border-4 border-pink-400 mx-auto mb-1 rounded-sm overflow-hidden">
                            @if ($top5[$index]->student->photo)
                                <img src="{{ asset('storage/' . $top5[$index]->student->photo) }}" 
                                     alt="{{ $top5[$index]->student->full_name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center 
                                            bg-gradient-to-br {{ strtolower($top5[$index]->student->gender ?? '') === 'female' 
                                                ? 'from-pink-100 to-rose-100 text-pink-700' 
                                                : 'from-blue-100 to-indigo-100 text-blue-700' }} 
                                            text-2xl font-bold">
                                    {{ strtoupper(substr($top5[$index]->student->first_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="w-20 border-b-2 border-blue-400 mx-auto mb-1"></div>
                        <p class="text-xs font-bold">{{ $top5[$index]->student->full_name }}</p>
                    </div>
                @endif
            @endforeach
        </div>

        {{-- 4th & 5th (Side by side) --}}
        <div class="flex justify-around gap-4">
            @foreach ([3, 4] as $index)
                @if (isset($top5[$index]))
                    <div class="text-center">
                        <p class="text-blue-700 font-bold text-base underline mb-1">{{ $index + 1 }}</p>
                        <div class="w-20 h-28 border-4 border-pink-400 mx-auto mb-1 rounded-sm overflow-hidden">
                            @if ($top5[$index]->student->photo)
                                <img src="{{ asset('storage/' . $top5[$index]->student->photo) }}" 
                                     alt="{{ $top5[$index]->student->full_name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center 
                                            bg-gradient-to-br {{ strtolower($top5[$index]->student->gender ?? '') === 'female' 
                                                ? 'from-pink-100 to-rose-100 text-pink-700' 
                                                : 'from-blue-100 to-indigo-100 text-blue-700' }} 
                                            text-2xl font-bold">
                                    {{ strtoupper(substr($top5[$index]->student->first_name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <div class="w-20 border-b-2 border-blue-400 mx-auto mb-1"></div>
                        <p class="text-xs font-bold">{{ $top5[$index]->student->full_name }}</p>
                    </div>
                @endif
            @endforeach
        </div>

    </div>
    @endif

    {{-- Signatures --}}
    <div class="mt-4 grid grid-cols-2 gap-4 text-xs">
        <div class="text-center">
            <p>បានឃើញនិងឯកភាព</p>
            <p class="font-bold mt-1">នាយកសាលា</p>
            <div class="h-10"></div>
        </div>

        <div class="text-center text-blue-700">
            <p>{{ config('app.school_city', 'ក្រុងបាត់ដំបង, ') }} {{ KhmerDate::format($reportDate) }}</p>
            <p class="font-bold mt-1">គ្រូទទួលបន្ទុកថ្នាក់</p>
            <div class="h-10"></div>
            <p class="font-semibold">{{ auth()->user()->name }}</p>
        </div>
    </div>

</div>