@extends('layouts.admin', ['title' => 'My Students'])

@section('content')

{{-- Page Header / Toolbar --}}
<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 shadow-sm">
    <div class="flex flex-col lg:flex-row lg:items-center gap-4">

        {{-- Title + total badge --}}
        <div class="flex items-center gap-3 flex-shrink-0">
            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <i class="ti ti-school text-green-600 text-xl"></i>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-800 leading-tight">My Students</h1>
                <span class="inline-flex items-center gap-1 mt-0.5 text-xs font-semibold text-green-700 bg-green-50 border border-green-200 rounded-full px-2 py-0.5">
                    <i class="ti ti-users text-sm"></i> {{ $students->total() }} total
                </span>
            </div>
        </div>

        {{-- Search + Filter form --}}
        <form method="GET" action="{{ route('teacher.students.index') }}"
              class="flex flex-col sm:flex-row gap-3 flex-1">

            <div class="relative flex-1">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name or ID…"
                    class="w-full border border-gray-300 rounded-full pl-9 pr-3 py-2.5 text-sm bg-gray-50
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white"
                />
            </div>

            <div class="relative sm:w-64">
                <i class="ti ti-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
                <select
                    name="class_id"
                    onchange="this.form.submit()"
                    class="w-full border border-gray-300 rounded-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 appearance-none
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white"
                >
                    <option value="">— All My Classes —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} ({{ $class->grade->name }}) · {{ $class->academicYear->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700
                           text-white text-sm font-semibold rounded-full transition-colors flex-shrink-0">
                <i class="ti ti-search text-base"></i>
                <span class="hidden sm:inline">Search</span>
            </button>

            @if ($search || $classId)
                <a href="{{ route('teacher.students.index') }}"
                   class="flex items-center justify-center gap-2 px-4 py-2.5 border border-gray-300 hover:bg-gray-50
                          text-gray-600 text-sm font-medium rounded-full transition-colors flex-shrink-0">
                    <i class="ti ti-x text-base"></i>
                    <span class="hidden sm:inline">Clear</span>
                </a>
            @endif
        </form>

        {{-- Action icon buttons (UI only — wire up when ready) --}}
        <div class="flex items-center gap-1.5 flex-shrink-0 justify-end">
            <button type="button" title="Refresh" onclick="window.location.reload()"
                    class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200
                           text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors">
                <i class="ti ti-refresh text-base"></i>
            </button>
            <button type="button" title="Export" disabled
                    class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200
                           text-gray-300 cursor-not-allowed">
                <i class="ti ti-download text-base"></i>
            </button>
            <button type="button" title="Import" disabled
                    class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200
                           text-gray-300 cursor-not-allowed">
                <i class="ti ti-upload text-base"></i>
            </button>
        </div>
    </div>
</div>

{{-- Alert --}}
@if (session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif

{{-- Table --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
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
                                          border border-gray-300 rounded-full hover:bg-gray-50 transition-colors">
                                    <i class="ti ti-eye text-sm"></i> View
                                </a>
                                <a href="{{ route('teacher.students.edit', $student) }}"
                                   class="flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-green-700
                                          border border-green-300 rounded-full hover:bg-green-50 transition-colors">
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
    </div>

    {{-- Pagination --}}
    @if ($students->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $students->links() }}
        </div>
    @endif
</div>

@endsection
