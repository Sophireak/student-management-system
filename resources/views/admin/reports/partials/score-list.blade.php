<style>
    @page { size: A4 landscape; margin: 0.8cm; }
    .rotate-text {
        writing-mode: vertical-rl;
        transform: rotate(180deg);
        white-space: nowrap;
    }
    .report-table { font-size: 10px; }
    .report-table th, .report-table td { border: 1px solid #1e3a8a; }
</style>

{{-- School Header --}}
<div class="mb-3">
    <p class="text-sm">{{ config('app.school_name', 'ការិយាល័យអប់រំ យុវជន និងកីឡា ក្រុងបាត់ដំបង') }}</p>
    <p class="text-sm">បឋមសិក្សា</p>
    <p class="text-sm">{{ $class->name }}</p>
</div>

{{-- Title --}}
<div class="text-center mb-4">
    <h1 class="khmer-title text-2xl font-bold text-gray-800">
        តារាងស្រង់ពិន្ទុ {{ $periodLabel }}
    </h1>
    <p class="text-sm mt-1">ឆ្នាំសិក្សា {{ $academicYear->name }}</p>
</div>

{{-- Score Table --}}
<div class="overflow-x-auto">
    <table class="w-full report-table border-collapse">
        <thead>
            <tr class="bg-blue-50">
                <th rowspan="2" class="px-1 py-2 text-center font-semibold text-blue-900 w-10">ល.រ</th>
                <th rowspan="2" class="px-2 py-2 text-center font-semibold text-blue-900 min-w-32">គោត្តនាម នាម</th>
                <th rowspan="2" class="px-1 py-2 text-center font-semibold text-blue-900 w-12">ភេទ</th>

                {{-- Subject groups (simplified — group all subjects) --}}
                <th colspan="{{ $subjects->count() }}" class="px-2 py-2 text-center font-semibold text-blue-900">
                    មុខវិជ្ជា
                </th>

                {{-- Results --}}
                <th colspan="5" class="px-2 py-2 text-center font-semibold text-blue-900 bg-blue-100">
                    លទ្ធផល
                </th>
            </tr>
            <tr class="bg-blue-50">
                {{-- Subject vertical headers --}}
                @foreach ($subjects as $subject)
                    <th class="px-1 py-2 text-center align-middle" style="height: 100px; min-width: 30px;">
                        <div class="rotate-text text-[10px] font-semibold text-blue-900">
                            {{ $subject->name }}
                        </div>
                    </th>
                @endforeach

                <th class="px-1 py-2 text-center font-semibold text-blue-900 bg-blue-100 w-14">ពិន្ទុ</th>
                <th class="px-1 py-2 text-center font-semibold text-blue-900 bg-blue-100 w-14">មធ្យម</th>
                <th class="px-1 py-2 text-center font-semibold text-blue-900 bg-blue-100 w-14">ចំ.ថ្នាក់</th>
                <th class="px-1 py-2 text-center font-semibold text-blue-900 bg-blue-100 w-16">និទ្ទេស</th>
                <th class="px-1 py-2 text-center font-semibold text-blue-900 bg-blue-100 w-16">លទ្ធផល</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($enrollments as $index => $enrollment)
                @php
                    $sum = $summary[$enrollment->id] ?? [];
                    $avg = $sum['average'] ?? null;
                    $grade = \App\Helpers\ScoreHelper::grade($avg);
                @endphp
                <tr>
                    <td class="px-1 py-2 text-center">{{ $index + 1 }}</td>
                    <td class="px-2 py-2 whitespace-nowrap">{{ $enrollment->student->full_name }}</td>
                    <td class="px-1 py-2 text-center">
                        {{ strtolower($enrollment->student->gender ?? '') === 'female' ? 'ស្រី' : 'ប្រុស' }}
                    </td>

                    {{-- Subject scores --}}
                    @foreach ($subjects as $subject)
                        @php $score = $matrix[$enrollment->id][$subject->id] ?? null; @endphp
                        <td class="px-1 py-2 text-center">
                            @if ($score && $score->score !== null)
                                {{ number_format($score->score, 2) }}
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                    @endforeach

                    {{-- Results --}}
                    <td class="px-1 py-2 text-center bg-blue-50/30">{{ $sum['total'] ?? '—' }}</td>
                    <td class="px-1 py-2 text-center bg-blue-50/30 font-semibold">
                        {{ $avg !== null ? number_format($avg, 2) : '—' }}
                    </td>
                    <td class="px-1 py-2 text-center bg-blue-50/30 font-semibold">
                        {{ $sum['rank'] ?? '—' }}
                    </td>
                    <td class="px-1 py-2 text-center bg-blue-50/30 text-[9px]">
                        {{ $grade['kh'] }}
                    </td>
                    <td class="px-1 py-2 text-center bg-blue-50/30 text-[9px]">
                        @if ($avg !== null)
                            {{ $avg >= 5.00 ? 'ជាប់' : 'ធ្លាក់' }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Statistics --}}
@include('admin.reports.partials.statistics', ['statistics' => $statistics])

{{-- Signatures --}}
@include('admin.reports.partials.signatures')