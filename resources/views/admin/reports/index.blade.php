@extends('layouts.admin', ['title' => 'Reports'])

@section('content')

<div class="mb-6">
    <h2 class="text-lg font-semibold text-gray-700">Reports</h2>
    <p class="text-sm text-gray-400 mt-0.5">
        Overview of the active academic year.
    </p>
</div>

{{-- Quick links --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

    <a href="{{ route('admin.reports.attendance') }}"
       class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm
              hover:shadow-md transition-shadow group">
        <p class="text-2xl mb-2">✅</p>
        <p class="font-semibold text-gray-800 group-hover:text-blue-600">
            Attendance Reports
        </p>
        <p class="text-xs text-gray-400 mt-1">
            Class-wide attendance overview
        </p>
    </a>

    <a href="{{ route('admin.students.index') }}"
       class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm
              hover:shadow-md transition-shadow group">
        <p class="text-2xl mb-2">📋</p>
        <p class="font-semibold text-gray-800 group-hover:text-blue-600">
            Student Report Cards
        </p>
        <p class="text-xs text-gray-400 mt-1">
            Select a student to view their report
        </p>
    </a>

    <a href="{{ route('admin.classes.index') }}"
       class="bg-white border border-gray-200 rounded-lg p-5 shadow-sm
              hover:shadow-md transition-shadow group">
        <p class="text-2xl mb-2">📊</p>
        <p class="font-semibold text-gray-800 group-hover:text-blue-600">
            Class Performance
        </p>
        <p class="text-xs text-gray-400 mt-1">
            Select a class to view its score report
        </p>
    </a>

</div>

{{-- Active year class overview --}}
<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
    Active Year — Classes Overview
</h3>

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
            @forelse ($overview as $class)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $class->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $class->grade->name }}</td>
                    <td class="px-4 py-3">{{ $class->total_students }}</td>
                    <td class="px-4 py-3">{{ $class->total_sessions }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.reports.class', $class) }}"
                               class="text-xs px-2 py-1 bg-blue-100 text-blue-700
                                      rounded hover:bg-blue-200">
                                Scores
                            </a>
                            <a href="{{ route('admin.reports.class.attendance', $class) }}"
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
                        No active classes found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection