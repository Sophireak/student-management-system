<style>
    @page { size: A4 portrait; margin: 1cm; }
</style>

{{-- Cambodia Header --}}
<div class="text-center mb-4">
    <h2 class="khmer-title text-lg font-bold">ព្រះរាជាណាចក្រកម្ពុជា</h2>
    <p class="text-sm">ជាតិ សាសនា ព្រះមហាក្សត្រ</p>
    <div class="flex items-center justify-center gap-2 my-2">
        <div class="w-16 border-b border-gray-800"></div>
        <div class="text-gray-800">✦</div>
        <div class="w-16 border-b border-gray-800"></div>
    </div>
</div>

{{-- School Info --}}
<div class="mb-3">
    <p class="text-sm">{{ config('app.school_name', 'ការិយាល័យអប់រំ យុវជន និងកីឡា ក្រុងបាត់ដំបង') }}</p>
    <p class="text-sm">បឋមសិក្សា</p>
    <p class="text-sm">{{ $class->name }}</p>
</div>

{{-- Title --}}
<div class="text-center mb-6">
    <h1 class="khmer-title text-2xl font-bold text-gray-800">
        តារាងកិត្តិយស {{ $periodLabel }}
    </h1>
    <p class="text-sm mt-1">ឆ្នាំសិក្សា {{ $academicYear->name }}</p>
</div>

{{-- Top 5 Students Pyramid --}}
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
    <div class="text-center py-12">
        <p class="text-gray-400">មិនមានទិន្នន័យ</p>
    </div>
@else
<div class="space-y-6 my-8">

    {{-- 1st Place (Top) --}}
    @if (isset($top5[0]))
        <div class="flex justify-center">
            <div class="text-center">
                <p class="text-blue-700 font-bold text-lg underline mb-2">1</p>
                <div class="w-32 h-40 border-4 border-pink-400 bg-gray-100 mx-auto mb-2 rounded-sm">
                    {{-- Student photo placeholder --}}
                </div>
                <div class="w-32 border-b-4 border-blue-400 mx-auto mb-2"></div>
                <p class="text-sm font-bold">{{ $top5[0]->student->full_name }}</p>
                <p class="text-xs text-gray-500">
                    {{ strtolower($top5[0]->student->gender ?? '') === 'female' ? 'ស្រី' : 'ប្រុស' }}
                </p>
                <p class="text-xs mt-1">
                    ពិន្ទុ: {{ number_format($summary[$top5[0]->id]['average'] ?? 0, 2) }}
                </p>
            </div>
        </div>
    @endif

    {{-- 2nd & 3rd (Side by side) --}}
    <div class="flex justify-around">
        @if (isset($top5[1]))
            <div class="text-center">
                <p class="text-blue-700 font-bold text-lg underline mb-2">2</p>
                <div class="w-28 h-36 border-4 border-pink-400 bg-gray-100 mx-auto mb-2 rounded-sm"></div>
                <div class="w-28 border-b-4 border-blue-400 mx-auto mb-2"></div>
                <p class="text-sm font-bold">{{ $top5[1]->student->full_name }}</p>
                <p class="text-xs text-gray-500">
                    {{ strtolower($top5[1]->student->gender ?? '') === 'female' ? 'ស្រី' : 'ប្រុស' }}
                </p>
                <p class="text-xs mt-1">
                    ពិន្ទុ: {{ number_format($summary[$top5[1]->id]['average'] ?? 0, 2) }}
                </p>
            </div>
        @endif

        @if (isset($top5[2]))
            <div class="text-center">
                <p class="text-blue-700 font-bold text-lg underline mb-2">3</p>
                <div class="w-28 h-36 border-4 border-pink-400 bg-gray-100 mx-auto mb-2 rounded-sm"></div>
                <div class="w-28 border-b-4 border-blue-400 mx-auto mb-2"></div>
                <p class="text-sm font-bold">{{ $top5[2]->student->full_name }}</p>
                <p class="text-xs text-gray-500">
                    {{ strtolower($top5[2]->student->gender ?? '') === 'female' ? 'ស្រី' : 'ប្រុស' }}
                </p>
                <p class="text-xs mt-1">
                    ពិន្ទុ: {{ number_format($summary[$top5[2]->id]['average'] ?? 0, 2) }}
                </p>
            </div>
        @endif
    </div>

    {{-- 4th & 5th (Side by side) --}}
    <div class="flex justify-around">
        @if (isset($top5[3]))
            <div class="text-center">
                <p class="text-blue-700 font-bold text-lg underline mb-2">4</p>
                <div class="w-28 h-36 border-4 border-pink-400 bg-gray-100 mx-auto mb-2 rounded-sm"></div>
                <div class="w-28 border-b-4 border-blue-400 mx-auto mb-2"></div>
                <p class="text-sm font-bold">{{ $top5[3]->student->full_name }}</p>
                <p class="text-xs text-gray-500">
                    {{ strtolower($top5[3]->student->gender ?? '') === 'female' ? 'ស្រី' : 'ប្រុស' }}
                </p>
                <p class="text-xs mt-1">
                    ពិន្ទុ: {{ number_format($summary[$top5[3]->id]['average'] ?? 0, 2) }}
                </p>
            </div>
        @endif

        @if (isset($top5[4]))
            <div class="text-center">
                <p class="text-blue-700 font-bold text-lg underline mb-2">5</p>
                <div class="w-28 h-36 border-4 border-pink-400 bg-gray-100 mx-auto mb-2 rounded-sm"></div>
                <div class="w-28 border-b-4 border-blue-400 mx-auto mb-2"></div>
                <p class="text-sm font-bold">{{ $top5[4]->student->full_name }}</p>
                <p class="text-xs text-gray-500">
                    {{ strtolower($top5[4]->student->gender ?? '') === 'female' ? 'ស្រី' : 'ប្រុស' }}
                </p>
                <p class="text-xs mt-1">
                    ពិន្ទុ: {{ number_format($summary[$top5[4]->id]['average'] ?? 0, 2) }}
                </p>
            </div>
        @endif
    </div>

</div>
@endif

{{-- Signatures --}}
@include('admin.reports.partials.signatures')