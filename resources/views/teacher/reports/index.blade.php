@extends('layouts.admin', ['title' => 'Reports'])

@section('content')

<div class="mb-4">
    <h2 class="text-lg font-semibold text-gray-700">My Class Reports</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Reports for your assigned classes in the active academic year.
    </p>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Grade</th>
                <th class="px-4 py-3 text-left">Students</th>
                <th class="px-4 py-3 text-left">Sessions</th>
                <th class="px-4 py-3 text-left">Reports</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($classes as $class)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $class->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $class->grade->name }}</td>
                    <td class="px-4 py-3">{{ $class->total_students }}</td>
                    <td class="px-4 py-3">{{ $class->total_sessions }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('teacher.reports.class', $class) }}"
                               class="text-xs px-2 py-1 bg-blue-100 text-blue-700
                                      rounded hover:bg-blue-200">
                                Scores
                            </a>
                            <a href="{{ route('teacher.reports.class.attendance', $class) }}"
                               class="text-xs px-2 py-1 bg-green-100 text-green-700
                                      rounded hover:bg-green-200">
                                Attendance
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5"
                        class="px-4 py-6 text-center text-gray-400">
                        No active classes assigned to you.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection