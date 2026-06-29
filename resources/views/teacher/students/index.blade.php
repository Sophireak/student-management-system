@extends('layouts.admin', ['title' => 'My Students'])

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">My Students</h1>
        <p class="text-sm text-gray-500 mt-1">Students enrolled in your assigned classes.</p>
    </div>
</div>

{{-- Alert --}}
@if (session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif

{{-- Filters --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" action="{{ route('teacher.students.index') }}" class="flex flex-col sm:flex-row gap-3">

        {{-- Search --}}
        <div class="relative flex-1">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input
                type="text"
                name="search"
                value="{{ $search }}"
                placeholder="Search by name or ID…"
                class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            />
        </div>

        {{-- Class Filter --}}
        <div class="relative sm:w-64">
            <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <select
                name="class_id"
                onchange="this.form.submit()"
                class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2.5 text-sm
                       focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
            >
                <option value="">— All My Classes —</option>
                @foreach ($classes as $class)
                    <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                        {{ $class->name }} ({{ $class->grade->name }}) · {{ $class->academicYear->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Search Button --}}
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700
                       text-white text-sm font-semibold rounded-lg transition-colors">
            <i class="ti ti-search text-base"></i> Search
        </button>

        @if ($search || $classId)
            <a href="{{ route('teacher.students.index') }}"
               class="flex items-center gap-2 px-4 py-2.5 border border-gray-300 hover:bg-gray-50
                      text-gray-600 text-sm font-medium rounded-lg transition-colors">
                <i class="ti ti-x text-base"></i> Clear
            </a>
        @endif

    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Student ID</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Name</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Gender</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Class</th>
                <th class="text-left px-4 py-3 font-semibold text-gray-600">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($students as $student)
                @php
                    $enrollment = $student->enrollments
                        ->whereIn('class_id', $classes->pluck('id'))
                        ->sortByDesc('enrolled_at')
                        ->first();
                @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $student->student_id }}</td>
                    <td class="px-4 py-3 font-medium text-gray-800">{{ $student->full_name }}</td>
                    <td class="px-4 py-3 text-gray-600 capitalize">{{ $student->gender ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-600">
                        @if ($enrollment)
                            {{ $enrollment->schoolClass->name }}
                            <span class="text-xs text-gray-400">({{ $enrollment->schoolClass->grade->name }})</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('teacher.students.show', $student) }}"
                               class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-gray-600
                                      border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="ti ti-eye text-sm"></i> View
                            </a>
                            <a href="{{ route('teacher.students.edit', $student) }}"
                               class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700
                                      border border-green-300 rounded-lg hover:bg-green-50 transition-colors">
                                <i class="ti ti-pencil text-sm"></i> Edit
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center text-gray-400">
                        <i class="ti ti-users-off text-3xl block mb-2"></i>
                        No students found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if ($students->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $students->links() }}
        </div>
    @endif
</div>

@endsection