@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')

    {{-- Stats row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-admin.stat-card label="Students"    value="{{ $totalStudents }}"    icon="👧"   color="blue" />
        <x-admin.stat-card label="Teachers"    value="{{ $totalTeachers }}"    icon="👩‍🏫" color="green" />
        <x-admin.stat-card label="Classes"     value="{{ $totalClasses }}"     icon="🏛️"  color="purple" />
        <x-admin.stat-card label="Enrollments" value="{{ $totalEnrollments }}" icon="📋"  color="yellow" />
    </div>

    {{-- Active academic year --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-6">
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">
            Active Academic Year
        </h2>
        <p class="text-xl font-bold text-gray-800">
            {{ $activeYear?->name ?? 'No active year set' }}
        </p>
        @if ($activeYear)
            <p class="text-sm text-gray-500 mt-1">
                {{ $activeYear->start_date->format('M d, Y') }}
                —
                {{ $activeYear->end_date->format('M d, Y') }}
            </p>
        @endif
    </div>

@endsection