@extends('layouts.admin', ['title' => 'Enter Scores — ' . $subject->name])

@section('content')

@php
    $routePrefix = auth()->user()->isAdmin() ? 'admin' : 'teacher';
    $backUrl     = route($routePrefix . '.scores.index', [
        'class_id' => $class->id,
        'period'   => $selectedPeriod,
    ]);
@endphp

{{-- Back Link --}}
<a href="{{ $backUrl }}"
   class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="ti ti-arrow-left text-base"></i> Back to Score Dashboard
</a>

{{-- Header --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        {{-- Subject Info --}}
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-book text-green-600 text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $subject->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $class->name }} · {{ $class->grade->name }} · {{ $periodLabel }}
                </p>
                <div class="flex items-center gap-2 mt-2">
                    <span class="text-xs font-medium px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md">
                        Max Score: 10
                    </span>
                    <span class="text-xs font-medium px-2 py-0.5 bg-blue-50 text-blue-700 rounded-md">
                        @if ($subject->isNumeric()) Numeric
                        @elseif ($subject->isGrade()) Grade-based
                        @else Pass / Fail
                        @endif
                    </span>
                </div>
            </div>
        </div>

        {{-- Lock Status --}}
        @if ($isLocked)
            <span class="flex items-center gap-2 px-3 py-1.5 text-xs font-semibold bg-red-50 text-red-700 border border-red-200 rounded-lg">
                <i class="ti ti-lock text-sm"></i> Locked — Read Only
            </span>
        @endif
    </div>
</div>



{{-- Empty State --}}
@if ($enrollments->isEmpty())
    <div class="bg-white rounded-xl border border-gray-200 px-5 py-16 text-center">
        <i class="ti ti-users-off text-5xl text-gray-300 block mb-3"></i>
        <p class="text-gray-500 text-sm">No active students in this class.</p>
    </div>

{{-- Input Form --}}
@else
    <form method="POST" action="{{ route($routePrefix . '.scores.save') }}" id="score-form">
        @csrf
        <input type="hidden" name="class_id"         value="{{ $class->id }}">
        <input type="hidden" name="subject_id"       value="{{ $subject->id }}">
        <input type="hidden" name="period"           value="{{ $selectedPeriod }}">
        <input type="hidden" name="save_and_next"    value="0" id="save-and-next">
        <input type="hidden" name="next_subject_id"  value="{{ $nextSubject?->id }}">

        {{-- Score Table --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-16">No.</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Student Name</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-20">Gender</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-40">Score</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase w-32">Grade</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($enrollments as $index => $enrollment)
                            @php
                                $existing = $scores[$enrollment->id] ?? null;
                                $hasValue = $existing !== null && (
                                    $existing->score !== null ||
                                    $existing->grade !== null ||
                                    $existing->pass_fail !== null
                                );
                            @endphp

                            <tr class="hover:bg-gray-50">
                                {{-- No --}}
                                <td class="px-4 py-2.5 text-center text-xs text-gray-400">
                                    {{ $index + 1 }}
                                </td>

                                {{-- Name --}}
                                <td class="px-4 py-2.5 font-medium text-gray-800">
                                    {{ $enrollment->student->full_name }}
                                </td>

                                {{-- Gender --}}
                                <td class="px-4 py-2.5 text-center">
    @php $g = strtolower($enrollment->student->gender ?? ''); @endphp
    @if ($g === 'male')
        <span class="text-blue-600 text-sm font-medium">ប្រុស</span>
    @elseif ($g === 'female')
        <span class="text-pink-600 text-sm font-medium">ស្រី</span>
    @else
        <span class="text-gray-300 text-xs">—</span>
    @endif
</td>

                                {{-- Score Input --}}
                                <td class="px-4 py-2 text-center">
                                    <input type="hidden" name="scores[{{ $index }}][enrollment_id]" value="{{ $enrollment->id }}">

                                    @if ($subject->isNumeric())
                                        <input type="number"
                                               name="scores[{{ $index }}][score]"
                                               value="{{ $existing?->score }}"
                                               min="0" max="10" step="0.01"
                                               placeholder="—"
                                               data-row="{{ $index }}"
                                               {{ $isLocked ? 'readonly' : '' }}
                                               class="score-input w-24 text-center border rounded-lg px-2 py-1.5 text-sm font-medium
                                                      focus:outline-none focus:ring-2 focus:ring-green-500
                                                      {{ $hasValue ? 'bg-green-50 border-green-200 text-green-700' : 'bg-white border-gray-200 text-gray-800' }}
                                                      {{ $isLocked ? 'opacity-60 cursor-not-allowed' : '' }}">

                                    @elseif ($subject->isGrade())
                                        <select name="scores[{{ $index }}][grade]"
                                                data-row="{{ $index }}"
                                                {{ $isLocked ? 'disabled' : '' }}
                                                class="score-input w-36 border rounded-lg px-2 py-1.5 text-sm
                                                       focus:outline-none focus:ring-2 focus:ring-green-500
                                                       {{ $hasValue ? 'bg-green-50 border-green-200' : 'bg-white border-gray-200' }}">
                                            <option value="">— Select —</option>
                                            @foreach (['Good', 'Satisfactory', 'Needs Improvement'] as $g)
                                                <option value="{{ $g }}" {{ $existing?->grade === $g ? 'selected' : '' }}>{{ $g }}</option>
                                            @endforeach
                                        </select>

                                    @else
                                        <div class="flex justify-center gap-2">
                                            @foreach (['Pass', 'Fail'] as $option)
                                                <label class="cursor-pointer">
                                                    <input type="radio"
                                                           name="scores[{{ $index }}][pass_fail]"
                                                           value="{{ $option }}"
                                                           {{ $existing?->pass_fail === $option ? 'checked' : '' }}
                                                           {{ $isLocked ? 'disabled' : '' }}
                                                           class="peer sr-only">
                                                    <span class="inline-block px-3 py-1 text-xs font-medium border rounded-lg
                                                                 peer-checked:bg-green-500 peer-checked:text-white peer-checked:border-green-500
                                                                 bg-white border-gray-200 text-gray-600 hover:bg-gray-50">
                                                        {{ $option }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </td>

                                {{-- Grade Preview --}}
                                <td class="px-4 py-2.5 text-center">
                                    @if ($subject->isNumeric() && $existing?->score !== null)
                                        @php $g = \App\Helpers\ScoreHelper::grade($existing->score); @endphp
                                        <span class="inline-block px-2 py-0.5 text-xs font-medium rounded-md
                                                     bg-{{ $g['color'] }}-50 text-{{ $g['color'] }}-700">
                                            {{ $g['kh'] }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Action Bar --}}
        @if (! $isLocked)
            <div class="mt-4 flex flex-wrap items-center justify-between gap-3 bg-white rounded-xl border border-gray-200 px-5 py-4">
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <i class="ti ti-info-circle"></i>
                    <span>Green cells are already saved. Empty scores will be skipped.</span>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Prev Subject --}}
                    @if ($prevSubject)
                        <a href="{{ route($routePrefix . '.scores.input', [
                                'class_id'   => $class->id,
                                'period'     => $selectedPeriod,
                                'subject_id' => $prevSubject->id,
                            ]) }}"
                           class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                            <i class="ti ti-chevron-left text-base"></i> {{ Str::limit($prevSubject->name, 12) }}
                        </a>
                    @endif

                    {{-- Save --}}
                    <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-white border border-green-600 text-green-700 hover:bg-green-50 text-sm font-semibold rounded-lg transition-colors">
                        <i class="ti ti-device-floppy text-base"></i> Save
                    </button>

                    {{-- Save & Next --}}
                    @if ($nextSubject)
                        <button type="submit"
                                onclick="document.getElementById('save-and-next').value = '1'"
                                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            <i class="ti ti-device-floppy text-base"></i> Save & Next
                            <i class="ti ti-chevron-right text-base"></i>
                        </button>
                    @else
                        <button type="submit"
                                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                            <i class="ti ti-device-floppy text-base"></i> Save & Finish
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </form>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const inputs = document.querySelectorAll('.score-input:not([readonly]):not([disabled])');
    const total  = inputs.length;

    // Keyboard navigation
    inputs.forEach((input, index) => {
        input.addEventListener('keydown', function (e) {
            let nextIndex = null;

            if (e.key === 'Enter' || e.key === 'ArrowDown') {
                e.preventDefault();
                nextIndex = index + 1 < total ? index + 1 : 0;
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                nextIndex = index - 1 >= 0 ? index - 1 : total - 1;
            }

            if (nextIndex !== null) {
                const next = inputs[nextIndex];
                next.focus();
                if (next.select) next.select();
            }
        });

        // Visual saved indicator on change
        input.addEventListener('input', function () {
            const empty = this.value === '' || this.value === null;
            this.classList.toggle('bg-green-50', !empty);
            this.classList.toggle('border-green-200', !empty);
            this.classList.toggle('text-green-700', !empty);
            this.classList.toggle('bg-white', empty);
            this.classList.toggle('border-gray-200', empty);
            this.classList.toggle('text-gray-800', empty);
        });
    });

    // Auto-select on focus
    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            if (this.select) this.select();
        });
    });

    // Unsaved changes warning
    let formChanged = false;
    inputs.forEach(input => {
        input.addEventListener('change', () => formChanged = true);
    });

    document.getElementById('score-form')?.addEventListener('submit', () => formChanged = false);

    window.addEventListener('beforeunload', function (e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
});
</script>
@endpush

@endsection 