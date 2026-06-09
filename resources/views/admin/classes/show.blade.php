@extends('layouts.admin', ['title' => $class->name])

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.classes.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Classes
    </a>
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-building text-green-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $class->name }}</h1>
                <p class="text-sm text-gray-400 mt-0.5">{{ $class->grade->name }} · {{ $class->academicYear->name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.classes.teachers.create', $class) }}"
               class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i class="ti ti-user-plus text-base"></i> Assign Teacher
            </a>
            <a href="{{ route('admin.classes.edit', $class) }}"
               class="flex items-center gap-2 px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg border border-yellow-200 transition-colors">
                <i class="ti ti-pencil text-base"></i> Edit
            </a>
        </div>
    </div>
</div>

@if (session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Class Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Class Information</h2>

        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-building text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Class Name</p>
                    <p class="text-sm font-medium text-gray-700">{{ $class->name }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-award text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Grade</p>
                    <p class="text-sm font-medium text-gray-700">{{ $class->grade->name }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-calendar text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Academic Year</p>
                    <p class="text-sm font-medium text-gray-700">{{ $class->academicYear->name }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-users text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Capacity</p>
                    <p class="text-sm font-medium text-gray-700">{{ $class->capacity ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Teachers --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Assigned Teachers</h2>

        @forelse ($class->classTeachers()->with('teacher.user')->get() as $ct)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="ti ti-user text-green-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $ct->teacher->user->name }}</p>
                        <p class="text-xs text-gray-400">{{ $ct->teacher->user->email }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if ($ct->is_primary)
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Primary</span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Assistant</span>
                    @endif
                    <form method="POST" action="{{ route('admin.classes.teachers.destroy', [$class, $ct]) }}"
                          onsubmit="return confirm('Remove this teacher?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors" title="Remove">
                            <i class="ti ti-x text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="py-10 text-center">
                <i class="ti ti-user-off text-4xl text-gray-300 block mb-2"></i>
                <p class="text-sm text-gray-400">No teachers assigned yet.</p>
                <a href="{{ route('admin.classes.teachers.create', $class) }}"
                   class="mt-3 inline-flex items-center gap-1 text-sm text-green-600 hover:underline">
                    <i class="ti ti-user-plus"></i> Assign a teacher
                </a>
            </div>
        @endforelse
    </div>

</div>

@endsection
