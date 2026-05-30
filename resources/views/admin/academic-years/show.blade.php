@extends('layouts.admin', ['title' => $academicYear->name])

@section('content')

<div class="max-w-lg">
    <a href="{{ route('admin.academic-years.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Academic Years
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-4">

        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">{{ $academicYear->name }}</h2>
            @if ($academicYear->is_active)
                <span class="px-2 py-1 text-xs font-semibold bg-green-100 text-green-700 rounded-full">
                    Active
                </span>
            @endif
        </div>

        <div class="text-sm text-gray-600 space-y-2">
            <p><span class="font-medium text-gray-700">Start Date:</span>
               {{ $academicYear->start_date->format('M d, Y') }}</p>
            <p><span class="font-medium text-gray-700">End Date:</span>
               {{ $academicYear->end_date->format('M d, Y') }}</p>
            <p><span class="font-medium text-gray-700">Total Classes:</span>
               {{ $academicYear->classes_count }}</p>
        </div>

        <div class="flex gap-2 pt-2">
            <a href="{{ route('admin.academic-years.edit', $academicYear) }}"
               class="px-4 py-2 bg-yellow-100 text-yellow-700 text-sm rounded-md hover:bg-yellow-200">
                Edit
            </a>
        </div>
    </div>
</div>

@endsection