@extends('layouts.admin', ['title' => 'Enrollments'])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-700">Enrollments</h2>
    <a href="{{ route('admin.enrollments.create') }}"
       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
        + New Enrollment
    </a>
</div>

<div class="mb-4">
    <form method="GET" action="" class="flex flex-wrap gap-2 items-end">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Search by student name or ID..."
               class="border border-gray-300 rounded px-3 py-1.5 text-sm min-w-64
                      focus:outline-none focus:ring-2 focus:ring-blue-400">
        <select name="status"
                class="border border-gray-300 rounded px-2 py-1.5 text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Status</option>
            <option value="active"      {{ ($status ?? '') === 'active'      ? 'selected' : '' }}>Active</option>
            <option value="transferred" {{ ($status ?? '') === 'transferred' ? 'selected' : '' }}>Transferred</option>
            <option value="dropped"     {{ ($status ?? '') === 'dropped'     ? 'selected' : '' }}>Dropped</option>
        </select>
        <button type="submit"
                class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
            Search
        </button>
        @if (($search ?? false) || ($status ?? false))
            <a href="{{ route('admin.enrollments.index') }}"
               class="px-4 py-1.5 bg-gray-100 text-gray-600 text-sm rounded hover:bg-gray-200">
                Clear
            </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Student</th>
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Grade</th>
                <th class="px-4 py-3 text-left">Year</th>
                <th class="px-4 py-3 text-left">Enrolled</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($enrollments as $enrollment)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3">
                        <div class="font-medium">{{ $enrollment->student->full_name }}</div>
                        <div class="text-xs text-gray-400 font-mono">{{ $enrollment->student->student_id }}</div>
                    </td>
                    <td class="px-4 py-3">{{ $enrollment->schoolClass->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $enrollment->schoolClass->grade->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $enrollment->schoolClass->academicYear->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $enrollment->enrolled_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $enrollment->status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-500' }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.students.show', $enrollment->student) }}"
                               class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                                Student
                            </a>
                            <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                               class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                                Edit
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-400">No enrollments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $enrollments->links() }}</div>

@endsection