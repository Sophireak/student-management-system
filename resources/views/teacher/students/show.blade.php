@extends('layouts.teacher', ['title' => $student->full_name])

@section('content')

{{-- Page Header --}}
<div class="bg-white rounded-2xl border border-gray-200 p-4 mb-5 shadow-sm flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('teacher.students.index') }}"
           class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors flex-shrink-0">
            <i class="ti ti-arrow-left text-base"></i>
        </a>
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
            <i class="ti ti-user text-green-600 text-xl"></i>
        </div>
        <h1 class="text-lg font-bold text-gray-800 leading-tight">{{ $student->full_name }}</h1>
    </div>
    <a href="{{ route('teacher.students.edit', $student) }}"
       class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700
              text-white text-sm font-semibold rounded-full transition-colors">
        <i class="ti ti-pencil text-base"></i> Edit
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Personal Info --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Personal Information</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Student ID</dt>
                <dd class="text-sm font-mono font-medium text-gray-700">{{ $student->student_id }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Full Name</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $student->full_name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Date of Birth</dt>
                <dd class="text-sm text-gray-700">
                    {{ $student->date_of_birth?->format('d M Y') ?? '—' }}
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Gender</dt>
                <dd class="text-sm text-gray-700 capitalize">{{ $student->gender ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Phone</dt>
                <dd class="text-sm text-gray-700">{{ $student->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Address</dt>
                <dd class="text-sm text-gray-700">{{ $student->address ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Guardian Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Guardian</h2>
        <dl class="space-y-3">
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Name</dt>
                <dd class="text-sm font-medium text-gray-700">{{ $student->guardian_name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Phone</dt>
                <dd class="text-sm text-gray-700">{{ $student->guardian_phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-400 mb-0.5">Relationship</dt>
                <dd class="text-sm text-gray-700 capitalize">{{ $student->guardian_relationship ?? '—' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Enrollment History --}}
    <div class="lg:col-span-3 bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Enrollment History</h2>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Class</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Grade</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Academic Year</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Enrolled</th>
                    <th class="text-left px-4 py-3 font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($student->enrollments->sortByDesc('enrolled_at') as $enrollment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $enrollment->schoolClass->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $enrollment->schoolClass->grade->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $enrollment->schoolClass->academicYear->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $enrollment->enrolled_at?->format('d M Y') ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $enrollment->status === 'active'
                                    ? 'bg-green-50 text-green-700 border border-green-200'
                                    : 'bg-gray-100 text-gray-500' }}">
                                {{ ucfirst($enrollment->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">No enrollment records.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


@endsection