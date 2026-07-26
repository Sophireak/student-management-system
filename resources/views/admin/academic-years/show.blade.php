@extends('layouts.admin', ['title' => $academicYear->name])

@section('content')

{{-- Back + Actions --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.academic-years.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Academic Years
    </a>
    <div class="flex items-center gap-2">
        @if(!$academicYear->is_active)
            <form method="POST" action="{{ route('admin.academic-years.activate', $academicYear) }}"
                  onsubmit="return confirm('Set {{ $academicYear->name }} as the active year?')">
                @csrf @method('PATCH')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700
                               text-white text-sm font-semibold rounded-xl transition-all shadow-sm
                               hover:shadow-green-500/20 active:scale-[0.98]">
                    <i class="ti ti-check text-lg"></i>
                    Activate
                </button>
            </form>
        @endif
        <a href="{{ route('admin.academic-years.edit', $academicYear) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200
                  text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 
                  transition-all active:scale-[0.98]">
            <i class="ti ti-pencil text-lg"></i>
            Edit
        </a>
    </div>
</div>

{{-- Duration Helper --}}
@php
   $hasDates = $academicYear->start_date && $academicYear->end_date;
$durationText = '—';
$durationShort = '—';

if ($hasDates) {
    $diff = $academicYear->start_date->diff($academicYear->end_date);
    $years = $diff->y;
    $months = $diff->m;
    $days = $diff->d;

    // Full text: "1 year 2 months 15 days"
    $parts = [];
    if ($years > 0) $parts[] = $years . ' year' . ($years > 1 ? 's' : '');
    if ($months > 0) $parts[] = $months . ' month' . ($months > 1 ? 's' : '');
    if ($days > 0) $parts[] = $days . ' day' . ($days > 1 ? 's' : '');
    $durationText = count($parts) > 0 ? implode(' ', $parts) : '0 days';

    // Short text: "1y 2m 15d"
    $shortParts = [];
    if ($years > 0) $shortParts[] = $years . 'y';
    if ($months > 0) $shortParts[] = $months . 'm';
    if ($days > 0) $shortParts[] = $days . 'd';
    $durationShort = count($shortParts) > 0 ? implode(' ', $shortParts) : '0d';
}
@endphp

{{-- Hero Card --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">

    <div class="h-28 relative
                {{ $academicYear->is_active 
                    ? 'bg-gradient-to-r from-green-600 to-emerald-500' 
                    : 'bg-gradient-to-r from-gray-500 to-gray-600' }}">
    </div>

    <div class="relative px-6 pb-6">
        <div class="-mt-10 mb-4 flex items-end justify-between">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center 
                        font-extrabold text-3xl shadow-lg ring-4 ring-white flex-shrink-0
                        {{ $academicYear->is_active 
                            ? 'bg-gradient-to-br from-green-100 to-emerald-100 text-green-700' 
                            : 'bg-gradient-to-br from-gray-100 to-gray-200 text-gray-500' }}">
                <i class="ti ti-calendar"></i>
            </div>
            <div class="flex items-center gap-2 pb-1">
                @if($academicYear->is_active)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs 
                                 font-bold bg-green-50 text-green-700 border border-green-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Active Year
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs 
                                 font-bold bg-gray-50 text-gray-500 border border-gray-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        Inactive
                    </span>
                @endif
            </div>
        </div>

        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                {{ $academicYear->name }}
            </h1>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                @if($hasDates)
                    <span class="flex items-center gap-1.5 text-sm text-gray-500">
                        <i class="ti ti-calendar-event text-gray-400 text-xs"></i>
                        {{ $academicYear->start_date->format('M d, Y') }}
                    </span>
                    <span class="text-gray-300">→</span>
                    <span class="flex items-center gap-1.5 text-sm text-gray-500">
                        <i class="ti ti-calendar-due text-gray-400 text-xs"></i>
                        {{ $academicYear->end_date->format('M d, Y') }}
                    </span>
                    <span class="text-gray-300">·</span>
                    <span class="text-sm text-gray-400">
                        {{ $durationText }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Stats Row --}}
@php
    $classes = $academicYear->classes()->with('grade')->withCount([
        'enrollments as active_students' => fn($q) => $q->where('status', 'active')
    ])->get();
    $totalClasses = $classes->count();
    $totalStudents = $classes->sum('active_students');
    $totalGrades = $classes->pluck('grade.name')->unique()->count();
@endphp

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-building text-purple-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">{{ $totalClasses }}</p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Classes</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-users text-blue-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">{{ $totalStudents }}</p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Students</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-award text-indigo-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">{{ $totalGrades }}</p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Grades</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-clock text-amber-500 text-lg"></i>
        </div>
        <p class="text-lg font-extrabold text-gray-800">{{ $durationShort }}</p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Duration</p>
    </div>

</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Year Details --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-amber-50 flex items-center justify-center">
                <i class="ti ti-info-circle text-amber-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Year Details</h2>
        </div>
        <div class="p-5 space-y-4">
            @php
                $details = [
                    ['icon' => 'ti-calendar', 'label' => 'Year Name', 'value' => $academicYear->name],
                    ['icon' => 'ti-calendar-event', 'label' => 'Start Date', 'value' => $academicYear->start_date?->format('M d, Y') ?? '—'],
                    ['icon' => 'ti-calendar-due', 'label' => 'End Date', 'value' => $academicYear->end_date?->format('M d, Y') ?? '—'],
                    ['icon' => 'ti-clock', 'label' => 'Duration', 'value' => $durationText],
                    ['icon' => 'ti-calendar-plus', 'label' => 'Created', 'value' => $academicYear->created_at->format('M d, Y')],
                    ['icon' => 'ti-refresh', 'label' => 'Last Updated', 'value' => $academicYear->updated_at->diffForHumans()],
                ];
            @endphp

            @foreach ($details as $detail)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center 
                                justify-center flex-shrink-0 border border-gray-100">
                        <i class="ti {{ $detail['icon'] }} text-gray-400 text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                            {{ $detail['label'] }}
                        </p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">
                            {{ $detail['value'] }}
                        </p>
                    </div>
                </div>
            @endforeach

            {{-- Status --}}
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center 
                            justify-center flex-shrink-0 border border-gray-100">
                    <i class="ti ti-toggle-right text-gray-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</p>
                    @if($academicYear->is_active)
                        <span class="mt-1 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                     font-bold bg-green-50 text-green-700 border border-green-100">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                            Active
                        </span>
                    @else
                        <span class="mt-1 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                     font-bold bg-gray-50 text-gray-500 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Inactive
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Classes --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="ti ti-building text-purple-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Classes This Year</h2>
            </div>
            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">
                {{ $totalClasses }} total
            </span>
        </div>

        @if($classes->isNotEmpty())
            <div class="divide-y divide-gray-100">
                @foreach ($classes->sortBy('grade.name') as $class)
                    <div class="flex items-center justify-between px-5 py-4 
                                hover:bg-gray-50/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br 
                                        from-purple-100 to-indigo-100 text-purple-700 
                                        flex items-center justify-center font-bold flex-shrink-0">
                                {{ strtoupper(substr($class->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ $class->name }}
                                </p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] 
                                                 font-semibold bg-indigo-50 text-indigo-600">
                                        {{ $class->grade->name }}
                                    </span>
                                    <span class="text-gray-300">·</span>
                                    <span class="text-xs text-gray-400">
                                        {{ $class->active_students }} students
                                    </span>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.classes.show', $class) }}"
                           class="w-7 h-7 rounded-lg flex items-center justify-center 
                                  text-gray-400 hover:text-blue-600 hover:bg-blue-50 
                                  transition-colors opacity-0 group-hover:opacity-100">
                            <i class="ti ti-arrow-right text-sm"></i>
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center 
                            justify-center mx-auto mb-4 border border-gray-100">
                    <i class="ti ti-building-off text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-800 mb-1">No classes yet</h3>
                <p class="text-sm text-gray-500 mb-4">
                    No classes have been created for this academic year.
                </p>
                <a href="{{ route('admin.classes.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 
                          rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                    <i class="ti ti-building-plus"></i> Create Class
                </a>
            </div>
        @endif

    </div>

</div>

@endsection