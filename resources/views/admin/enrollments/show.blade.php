@extends('layouts.admin', ['title' => 'Enrollment Details'])

@section('content')

{{-- Back + Actions --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.enrollments.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Enrollments
    </a>
    <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200
              text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 
              transition-all active:scale-[0.98]">
        <i class="ti ti-pencil text-lg"></i>
        Edit Enrollment
    </a>
</div>

{{-- Hero Card --}}
@php
    $statusConfig = match($enrollment->status) {
        'active'      => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-100', 'dot' => 'bg-green-500', 'label' => 'Active', 'banner' => 'from-green-600 to-emerald-500'],
        'transferred' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'dot' => 'bg-blue-500', 'label' => 'Transferred', 'banner' => 'from-blue-600 to-indigo-500'],
        'dropped'     => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'dot' => 'bg-red-500', 'label' => 'Dropped', 'banner' => 'from-red-600 to-rose-500'],
        default       => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400', 'label' => 'Unknown', 'banner' => 'from-gray-600 to-gray-500'],
    };
@endphp

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">

    {{-- Banner --}}
    <div class="h-28 bg-gradient-to-r {{ $statusConfig['banner'] }} relative"></div>

    {{-- Profile Content --}}
    <div class="relative px-6 pb-6">

        {{-- Avatar --}}
        <div class="-mt-10 mb-4 flex items-end justify-between">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center 
                        font-extrabold text-3xl shadow-lg ring-4 ring-white flex-shrink-0
                        {{ $enrollment->student->gender === 'female'
                            ? 'bg-gradient-to-br from-pink-100 to-rose-100 text-pink-700'
                            : 'bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700' }}">
                {{ strtoupper(substr($enrollment->student->first_name, 0, 1)) }}
            </div>
            <div class="flex items-center gap-2 pb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs 
                             font-bold border
                             {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                    {{ $statusConfig['label'] }}
                </span>
            </div>
        </div>

        {{-- Name & Info --}}
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                {{ $enrollment->student->full_name }}
            </h1>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs 
                             font-mono font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                    <i class="ti ti-id-badge text-gray-400"></i>
                    {{ $enrollment->student->student_id }}
                </span>
                <span class="text-gray-300">·</span>
                <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                    <i class="ti ti-building text-gray-400 text-xs"></i>
                    {{ $enrollment->schoolClass->name }}
                </span>
                <span class="text-gray-300">·</span>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                             font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                    <i class="ti ti-award text-indigo-400"></i>
                    {{ $enrollment->schoolClass->grade->name }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-building text-purple-500 text-lg"></i>
        </div>
        <p class="text-sm font-extrabold text-gray-800">
            {{ $enrollment->schoolClass->name }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Class</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-award text-indigo-500 text-lg"></i>
        </div>
        <p class="text-sm font-extrabold text-gray-800">
            {{ $enrollment->schoolClass->grade->name }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Grade</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-calendar text-amber-500 text-lg"></i>
        </div>
        <p class="text-sm font-extrabold text-gray-800">
            {{ $enrollment->schoolClass->academicYear->name }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Year</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-calendar-check text-green-500 text-lg"></i>
        </div>
        <p class="text-sm font-extrabold text-gray-800">
            {{ $enrollment->enrolled_at->format('M d') }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Enrolled</p>
    </div>

</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Enrollment Details --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
                <i class="ti ti-clipboard-list text-green-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Enrollment Details</h2>
        </div>
        <div class="p-5 space-y-4">
            @php
                $details = [
                    ['icon' => 'ti-user', 'label' => 'Student', 'value' => $enrollment->student->full_name],
                    ['icon' => 'ti-id-badge', 'label' => 'Student ID', 'value' => $enrollment->student->student_id, 'mono' => true],
                    ['icon' => 'ti-building', 'label' => 'Class', 'value' => $enrollment->schoolClass->name],
                    ['icon' => 'ti-award', 'label' => 'Grade', 'value' => $enrollment->schoolClass->grade->name],
                    ['icon' => 'ti-calendar', 'label' => 'Academic Year', 'value' => $enrollment->schoolClass->academicYear->name],
                    ['icon' => 'ti-calendar-check', 'label' => 'Enrolled On', 'value' => $enrollment->enrolled_at->format('M d, Y')],
                    ['icon' => 'ti-clock', 'label' => 'Duration', 'value' => $enrollment->enrolled_at->diffForHumans(null, true) . ' ago'],
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
                        <p class="text-sm font-medium text-gray-700 mt-0.5 
                                  {{ isset($detail['mono']) && $detail['mono'] ? 'font-mono' : '' }}">
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
                    <span class="mt-1 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                 font-bold border
                                 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                        {{ ucfirst($enrollment->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Quick Links + Actions --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Quick Navigation --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-compass text-blue-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Quick Navigation</h2>
            </div>
            <div class="p-3 space-y-1">
                <a href="{{ route('admin.students.show', $enrollment->student) }}"
                   class="flex items-center justify-between px-4 py-3 rounded-xl 
                          hover:bg-gray-50 text-sm text-gray-700 font-medium 
                          transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 group-hover:bg-blue-100 
                                    flex items-center justify-center transition-colors">
                            <i class="ti ti-user text-blue-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">View Student Profile</p>
                            <p class="text-xs text-gray-400">{{ $enrollment->student->full_name }}</p>
                        </div>
                    </div>
                    <i class="ti ti-chevron-right text-gray-300 group-hover:text-blue-500 
                              group-hover:translate-x-0.5 transition-all"></i>
                </a>

                <a href="{{ route('admin.classes.show', $enrollment->schoolClass) }}"
                   class="flex items-center justify-between px-4 py-3 rounded-xl 
                          hover:bg-gray-50 text-sm text-gray-700 font-medium 
                          transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-purple-50 group-hover:bg-purple-100 
                                    flex items-center justify-center transition-colors">
                            <i class="ti ti-building text-purple-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">View Class Details</p>
                            <p class="text-xs text-gray-400">{{ $enrollment->schoolClass->name }} · {{ $enrollment->schoolClass->grade->name }}</p>
                        </div>
                    </div>
                    <i class="ti ti-chevron-right text-gray-300 group-hover:text-purple-500 
                              group-hover:translate-x-0.5 transition-all"></i>
                </a>

                <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                   class="flex items-center justify-between px-4 py-3 rounded-xl 
                          hover:bg-gray-50 text-sm text-gray-700 font-medium 
                          transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 group-hover:bg-amber-100 
                                    flex items-center justify-center transition-colors">
                            <i class="ti ti-pencil text-amber-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Edit Enrollment</p>
                            <p class="text-xs text-gray-400">Change class, status, or date</p>
                        </div>
                    </div>
                    <i class="ti ti-chevron-right text-gray-300 group-hover:text-amber-500 
                              group-hover:translate-x-0.5 transition-all"></i>
                </a>
            </div>
        </div>

        {{-- Timeline --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="ti ti-clock text-green-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Timeline</h2>
            </div>
            <div class="p-5">
                <div class="relative pl-6 space-y-6">
                    {{-- Timeline line --}}
                    <div class="absolute left-[7px] top-2 bottom-2 w-px bg-gray-200"></div>

                    {{-- Enrolled --}}
                    <div class="relative flex items-start gap-3">
                        <div class="absolute -left-6 w-3.5 h-3.5 rounded-full bg-green-500 
                                    ring-4 ring-white flex-shrink-0 mt-0.5"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Student Enrolled</p>
                            <p class="text-xs text-gray-400">
                                {{ $enrollment->enrolled_at->format('M d, Y') }} · 
                                {{ $enrollment->enrolled_at->diffForHumans() }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Enrolled in {{ $enrollment->schoolClass->name }} 
                                ({{ $enrollment->schoolClass->grade->name }})
                            </p>
                        </div>
                    </div>

                    {{-- Current Status --}}
                    <div class="relative flex items-start gap-3">
                        <div class="absolute -left-6 w-3.5 h-3.5 rounded-full {{ $statusConfig['dot'] }} 
                                    ring-4 ring-white flex-shrink-0 mt-0.5"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">
                                Status: {{ ucfirst($enrollment->status) }}
                            </p>
                            <p class="text-xs text-gray-400">
                                Last updated {{ $enrollment->updated_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

@endsection