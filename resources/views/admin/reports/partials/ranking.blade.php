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
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-12">ល.រ</th>
            <th class="px-3 py-2 text-center font-semibold text-blue-900">គោត្តនាម នាម</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-14">ភេទ</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-16">ពិន្ទុ</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-16">មធ្យម</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-16">ចំ.ថ្នាក់</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-20">និទ្ទេស</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-20">លទ្ធផល</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-20">អវត្តមាន</th>
            <th class="px-2 py-2 text-center font-semibold text-blue-900 w-20">ផ្សេងៗ</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($ranked as $index => $enrollment)
            @php
                $sum = $summary[$enrollment->id] ?? [];
                $avg = $sum['average'] ?? null;
                $grade = \App\Helpers\ScoreHelper::grade($avg);
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
                <td class="px-2 py-2 text-center">—</td>
                <td class="px-2 py-2 text-center">—</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Statistics --}}
@include('admin.reports.partials.statistics', ['statistics' => $statistics])

{{-- Signatures --}}
@include('admin.reports.partials.signatures')