<style>
    @page { size: A4 portrait; margin: 1cm; }
    .ranking-table { font-size: 12px; }
    .ranking-table th, .ranking-table td { border: 1px solid #1e3a8a; }
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
<div class="text-center mb-4">
    <h1 class="khmer-title text-2xl font-bold text-gray-800">
        តារាងចំណាត់ថ្នាក់ {{ $periodLabel }}
    </h1>
    <p class="text-sm mt-1">ឆ្នាំសិក្សា {{ $academicYear->name }}</p>
</div>

{{-- Sort enrollments by rank --}}
@php
    $ranked = $enrollments->sortBy(function ($e) use ($summary) {
        return $summary[$e->id]['rank'] ?? 999;
    });
@endphp

{{-- Ranking Table --}}
<table class="w-full ranking-table border-collapse">
    <thead>
        <tr class="bg-blue-50">
            <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 w-12 border border-blue-900">ល.រ</th>
            <th rowspan="2" class="px-3 py-2 text-center font-semibold text-blue-900 border border-blue-900">គោត្តនាម នាម</th>
            <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 w-14 border border-blue-900">ភេទ</th>
            <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 w-16 border border-blue-900">ពិន្ទុ</th>
            <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 w-16 border border-blue-900">មធ្យម</th>
            <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 w-16 border border-blue-900">ចំ.ថ្នាក់</th>
            <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 w-20 border border-blue-900">និទ្ទេស</th>
            <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 w-20 border border-blue-900">លទ្ធផល</th>
            
            {{-- Absent parent header (spans 2 columns) --}}
            <th colspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 border border-blue-900">អវត្តមាន</th>
            
            <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 w-20 border border-blue-900">ផ្សេងៗ</th>
        </tr>
        <tr class="bg-blue-50">
            {{-- Sub-headers for Absent --}}
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-14 border border-blue-900">ច្បាប់</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-14 border border-blue-900">អច្បាប់</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($ranked as $index => $enrollment)
            @php
                $sum = $summary[$enrollment->id] ?? [];
                $avg = $sum['average'] ?? null;
                $grade = \App\Helpers\ScoreHelper::grade($avg);
                $attendance = $attendanceCounts[$enrollment->id] ?? ['absent' => 0, 'late' => 0, 'excused' => 0];
            @endphp
            <tr>
                <td class="px-2 py-2 text-center">{{ $index + 1 }}</td>
                <td class="px-3 py-2">{{ $enrollment->student->full_name }}</td>
                <td class="px-2 py-2 text-center">
                    {{ strtolower($enrollment->student->gender ?? '') === 'female' ? 'ស្រី' : 'ប្រុស' }}
                </td>
                <td class="px-2 py-2 text-center">{{ $sum['total'] ?? '—' }}</td>
                <td class="px-2 py-2 text-center font-semibold">
                    {{ $avg !== null ? number_format($avg, 2) : '—' }}
                </td>
                <td class="px-2 py-2 text-center font-bold">{{ $sum['rank'] ?? '—' }}</td>
                <td class="px-2 py-2 text-center text-xs">{{ $grade['kh'] }}</td>
                <td class="px-2 py-2 text-center text-xs">
                    @if ($avg !== null)
                        {{ $avg >= 5.00 ? 'ជាប់' : 'ធ្លាក់' }}
                    @else
                        —
                    @endif
                </td>

                {{-- ច្បាប់ (With permission = excused) --}}
                <td class="px-2 py-2 text-center">
                    {{ $attendance['excused'] > 0 ? $attendance['excused'] : '—' }}
                </td>

                {{-- អច្បាប់ (Without permission = absent) --}}
                <td class="px-2 py-2 text-center">
                    {{ $attendance['absent'] > 0 ? $attendance['absent'] : '—' }}
                </td>

                {{-- ផ្សេងៗ (Others: late) --}}
                <td class="px-2 py-2 text-center whitespace-nowrap">
                    @if ($attendance['late'] > 0)
                        <span class="text-[10px] text-gray-600">យឺត {{ $attendance['late'] }}</span>
                    @else
                        —
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Statistics --}}
@include('admin.reports.partials.statistics', ['statistics' => $statistics])

{{-- Signatures --}}
@include('admin.reports.partials.signatures')