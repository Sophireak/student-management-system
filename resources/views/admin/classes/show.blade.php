@extends('layouts.admin', ['title' => $class->name])

@section('content')

<div class="max-w-3xl">
    <a href="{{ route('admin.classes.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Classes
    </a>

    {{-- Class summary --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">
                    Class {{ $class->name }}
                </h2>
                <p class="text-sm text-gray-500 mt-0.5">
                    {{ $class->grade->name }}
                    · {{ $class->academicYear->name }}
                </p>
            </div>
            <a href="{{ route('admin.classes.edit', $class) }}"
               class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                Edit
            </a>
            <a href="{{ route('admin.score-report.index') }}"
               class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                Score Report
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Enrolled</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $class->enrollments->count() }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Capacity</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $class->capacity ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Teachers</p>
                <p class="font-bold text-gray-800 text-lg">
                    {{ $class->teachers->count() }}
                </p>
            </div>
        </div>
    </div>

    {{-- Assigned teachers --}}
    <div class="flex items-center justify-between mb-3">
    <h3 class="text-sm font-semibold text-gray-700">Assigned Teachers</h3>
    <a href="{{ route('admin.classes.teachers.index', $class) }}"
       class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
        Manage Teachers
    </a>
</div>
    {{-- <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Assigned Teachers</h3>
        @forelse ($class->teachers as $teacher)
            <div class="flex items-center justify-between py-2 border-b border-gray-100
                        last:border-0 text-sm">
                <span class="font-medium text-gray-800">{{ $teacher->user->name }}</span>
                @if ($teacher->pivot->is_primary)
                    <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                        Primary
                    </span>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400">No teachers assigned yet.</p>
        @endforelse
    </div> --}}

    {{-- Enrolled students --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Enrolled Students</h3>
            <a href="{{ route('admin.enrollments.create') }}"
               class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                + Enroll Student
            </a>
        </div>

        @forelse ($class->enrollments as $enrollment)
            <div class="flex items-center justify-between py-2 border-b border-gray-100
                        last:border-0 text-sm">
                <div>
                    <span class="font-medium text-gray-800">
                        {{ $enrollment->student->full_name }}
                    </span>
                    <span class="text-xs text-gray-400 ml-2">
                        {{ $enrollment->student->student_id }}
                    </span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 text-xs rounded-full
                        {{ $enrollment->status === 'active'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($enrollment->status) }}
                    </span>
                    <a href="{{ route('admin.enrollments.show', $enrollment) }}"
                       class="text-xs text-blue-500 hover:underline">
                        View
                    </a>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">No students enrolled yet.</p>
        @endforelse
    </div>

</div>

@endsection