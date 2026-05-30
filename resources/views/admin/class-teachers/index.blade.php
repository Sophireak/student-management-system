@extends('layouts.admin', ['title' => 'Teachers — ' . $class->name])

@section('content')

<div class="max-w-2xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
        <a href="{{ route('admin.classes.index') }}" class="hover:text-gray-700">Classes</a>
        <span>›</span>
        <a href="{{ route('admin.classes.show', $class) }}"
           class="hover:text-gray-700">
            {{ $class->name }}
        </a>
        <span>›</span>
        <span class="text-gray-700">Teachers</span>
    </div>

    {{-- Header --}}
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-lg font-semibold text-gray-700">
                Assigned Teachers
            </h2>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $class->grade->name }}
                · {{ $class->academicYear->name }}
            </p>
        </div>
        <a href="{{ route('admin.classes.teachers.create', $class) }}"
           class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
            + Assign Teacher
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

        @forelse ($class->classTeachers as $assignment)
            <div class="flex items-center justify-between px-5 py-4
                        border-b border-gray-100 last:border-0">

                <div class="flex items-center gap-3">
                    {{-- Avatar initial --}}
                    <div class="w-9 h-9 rounded-full bg-gray-200 text-gray-600
                                flex items-center justify-center font-bold text-sm flex-shrink-0">
                        {{ strtoupper(substr($assignment->teacher->user->name, 0, 1)) }}
                    </div>

                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            {{ $assignment->teacher->user->name }}
                        </p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <p class="text-xs text-gray-400">
                                {{ $assignment->teacher->user->email }}
                            </p>
                            @if ($assignment->teacher->employee_id)
                                <span class="text-gray-300">·</span>
                                <p class="text-xs text-gray-400">
                                    {{ $assignment->teacher->employee_id }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">

                    {{-- Primary badge --}}
                    @if ($assignment->is_primary)
                        <span class="px-2 py-0.5 text-xs font-semibold
                                     bg-blue-100 text-blue-700 rounded-full">
                            Primary
                        </span>
                    @else
                        {{-- Set as primary --}}
                        <form method="POST"
                              action="{{ route('admin.classes.teachers.setPrimary',
                                              [$class, $assignment]) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="text-xs px-2 py-1 bg-gray-100 text-gray-600
                                           rounded hover:bg-blue-100 hover:text-blue-700">
                                Set Primary
                            </button>
                        </form>
                    @endif

                    {{-- Remove --}}
                    <form method="POST"
                          action="{{ route('admin.classes.teachers.destroy',
                                          [$class, $assignment]) }}"
                          onsubmit="return confirm('Remove {{ $assignment->teacher->user->name }} from this class?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-xs px-2 py-1 bg-red-100 text-red-700
                                       rounded hover:bg-red-200">
                            Remove
                        </button>
                    </form>

                </div>
            </div>

        @empty
            <div class="px-5 py-8 text-center text-gray-400 text-sm">
                No teachers assigned yet.
                <a href="{{ route('admin.classes.teachers.create', $class) }}"
                   class="text-blue-500 hover:underline ml-1">
                    Assign one now.
                </a>
            </div>
        @endforelse

    </div>

    {{-- Subject note --}}
    <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-md text-xs text-blue-700">
        <p class="font-semibold mb-1">How subjects relate to teachers</p>
        <p>
            Teachers are assigned to a class, not directly to subjects.
            When a teacher creates an exam session or attendance session,
            they select the subject for that session.
            This means one teacher can cover multiple subjects in the same class.
        </p>
    </div>

</div>

@endsection