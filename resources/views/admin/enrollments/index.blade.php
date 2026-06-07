@extends('layouts.admin', ['title' => 'Enrollments'])

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Enrollments</h1>
        <p class="text-sm text-gray-500 mt-1">Manage all student enrollments</p>
    </div>
    <a href="{{ route('admin.enrollments.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
        <i class="ti ti-clipboard-plus text-base"></i> New Enrollment
    </a>
</div>

{{-- Search & Filter --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" action="" class="flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-48">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by student name or ID..."
                   class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div class="relative">
            <i class="ti ti-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <select name="status"
                    class="border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Status</option>
                <option value="active"      {{ ($status ?? '') === 'active'      ? 'selected' : '' }}>Active</option>
                <option value="transferred" {{ ($status ?? '') === 'transferred' ? 'selected' : '' }}>Transferred</option>
                <option value="dropped"     {{ ($status ?? '') === 'dropped'     ? 'selected' : '' }}>Dropped</option>
            </select>
        </div>
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="ti ti-search text-base"></i> Search
        </button>
        @if (($search ?? false) || ($status ?? false))
            <a href="{{ route('admin.enrollments.index') }}"
               class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                <i class="ti ti-x text-base"></i> Clear
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Class</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Grade</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Year</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Enrolled</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($enrollments as $enrollment)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-user text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $enrollment->student->full_name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $enrollment->student->student_id }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-700">{{ $enrollment->schoolClass->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $enrollment->schoolClass->grade->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $enrollment->schoolClass->academicYear->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $enrollment->enrolled_at->format('M d, Y') }}</td>
                    <td class="px-4 py-3">
                        @php
                            $statusColor = match($enrollment->status) {
                                'active'      => 'bg-green-100 text-green-700',
                                'transferred' => 'bg-blue-100 text-blue-700',
                                'dropped'     => 'bg-red-100 text-red-700',
                                default       => 'bg-gray-100 text-gray-500',
                            };
                        @endphp
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ $statusColor }}">
                            {{ ucfirst($enrollment->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.students.show', $enrollment->student) }}"
                               class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors" title="View Student">
                                <i class="ti ti-user text-sm"></i>
                            </a>
                            <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                               class="p-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 transition-colors" title="Edit">
                                <i class="ti ti-pencil text-sm"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center">
                        <i class="ti ti-clipboard-off text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-400 text-sm">No enrollments found.</p>
                        <a href="{{ route('admin.enrollments.create') }}"
                           class="mt-3 inline-flex items-center gap-1 text-sm text-green-600 hover:underline">
                            <i class="ti ti-clipboard-plus"></i> Add first enrollment
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $enrollments->links() }}</div>

@endsection
