@extends('layouts.admin', ['title' => $teacher->user->name])

@section('content')

<div class="max-w-xl">
    <a href="{{ route('admin.teachers.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Teachers
    </a>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">

        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-bold text-gray-800">{{ $teacher->user->name }}</h2>
            <a href="{{ route('admin.teachers.edit', $teacher) }}"
               class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                Edit
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm text-gray-600">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Email</p>
                <p class="font-medium text-gray-800">{{ $teacher->user->email }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Employee ID</p>
                <p class="font-medium text-gray-800">{{ $teacher->employee_id ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Phone</p>
                <p class="font-medium text-gray-800">{{ $teacher->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Gender</p>
                <p class="font-medium text-gray-800 capitalize">{{ $teacher->gender ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Date of Birth</p>
                <p class="font-medium text-gray-800">
                    {{ $teacher->date_of_birth?->format('M d, Y') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Address</p>
                <p class="font-medium text-gray-800">{{ $teacher->address ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Assigned classes --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Assigned Classes</h3>

        @forelse ($teacher->classes as $class)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 text-sm">
                <span class="font-medium text-gray-800">{{ $class->name }}</span>
                <div class="flex items-center gap-2 text-gray-500">
                    <span>{{ $class->grade->name }}</span>
                    <span>·</span>
                    <span>{{ $class->academicYear->name }}</span>
                    @if ($class->pivot->is_primary)
                        <span class="px-2 py-0.5 text-xs bg-blue-100 text-blue-600 rounded-full">
                            Primary
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">No classes assigned yet.</p>
        @endforelse
    </div>

</div>

@endsection