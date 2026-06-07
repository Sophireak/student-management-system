@extends('layouts.admin', ['title' => 'Enrollment Details'])

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.enrollments.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Enrollments
    </a>
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-clipboard-list text-green-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $enrollment->student->full_name }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ $enrollment->schoolClass->name }} · {{ $enrollment->schoolClass->academicYear->name }}</p>
            </div>
        </div>
        <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
           class="flex items-center gap-2 px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg border border-yellow-200 transition-colors">
            <i class="ti ti-pencil text-base"></i> Edit
        </a>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

    {{-- Enrollment Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Enrollment Information</h2>

        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-user text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Student</p>
                    <p class="text-sm font-medium text-gray-700">{{ $enrollment->student->full_name }}</p>
                    <p class="text-xs text-gray-400 font-mono">{{ $enrollment->student->student_id }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-building text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Class</p>
                    <p class="text-sm font-medium text-gray-700">{{ $enrollment->schoolClass->name }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-award text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Grade</p>
                    <p class="text-sm font-medium text-gray-700">{{ $enrollment->schoolClass->grade->name }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-calendar text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Academic Year</p>
                    <p class="text-sm font-medium text-gray-700">{{ $enrollment->schoolClass->academicYear->name }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-calendar-check text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Enrolled On</p>
                    <p class="text-sm font-medium text-gray-700">{{ $enrollment->enrolled_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-toggle-right text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Status</p>
                    @php
                        $statusColor = match($enrollment->status) {
                            'active'      => 'bg-green-100 text-green-700',
                            'transferred' => 'bg-blue-100 text-blue-700',
                            'dropped'     => 'bg-red-100 text-red-700',
                            default       => 'bg-gray-100 text-gray-500',
                        };
                    @endphp
                    <span class="mt-1 inline-block px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusColor }}">
                        {{ ucfirst($enrollment->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Links --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Quick Links</h2>
        <div class="space-y-2">
            <a href="{{ route('admin.students.show', $enrollment->student) }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 text-sm text-gray-700 font-medium transition-colors group">
                <div class="flex items-center gap-3">
                    <i class="ti ti-user text-gray-400 group-hover:text-green-600 text-base"></i>
                    View Student Profile
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>
            <a href="{{ route('admin.classes.show', $enrollment->schoolClass) }}"
               class="flex items-center justify-between px-4 py-3 rounded-lg hover:bg-gray-50 text-sm text-gray-700 font-medium transition-colors group">
                <div class="flex items-center gap-3">
                    <i class="ti ti-building text-gray-400 group-hover:text-green-600 text-base"></i>
                    View Class Details
                </div>
                <i class="ti ti-chevron-right text-gray-300 group-hover:text-green-500 text-sm"></i>
            </a>
        </div>
    </div>

</div>

@endsection
