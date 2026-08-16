@extends('layouts.teacher', ['title' => 'My Students'])

@section('content')

{{-- Toolbar --}}
<div class="mb-4">
    {{-- Tabs --}}
    <div class="inline-flex items-center bg-white border border-gray-200 
                rounded-xl p-1 shadow-sm overflow-x-auto w-full sm:w-auto">

        <a href="{{ route('teacher.students.index', array_merge(request()->query(), ['gender' => null])) }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap flex-1 sm:flex-initial justify-center
                  {{ !$gender ? 'bg-green-50 text-green-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
            <i class="ti ti-users text-base"></i>
            All
            <span class="text-xs font-bold px-1.5 py-0.5 rounded-md
                         {{ !$gender ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $totalCount }}
            </span>
        </a>

        <a href="{{ route('teacher.students.index', array_merge(request()->query(), ['gender' => 'male'])) }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap flex-1 sm:flex-initial justify-center
                  {{ $gender === 'male' ? 'bg-blue-50 text-blue-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
            <i class="ti ti-gender-male text-base"></i>
            Male
            <span class="text-xs font-bold px-1.5 py-0.5 rounded-md
                         {{ $gender === 'male' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $maleCount }}
            </span>
        </a>

        <a href="{{ route('teacher.students.index', array_merge(request()->query(), ['gender' => 'female'])) }}"
           class="flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold transition-all whitespace-nowrap flex-1 sm:flex-initial justify-center
                  {{ $gender === 'female' ? 'bg-pink-50 text-pink-700' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
            <i class="ti ti-gender-female text-base"></i>
            Female
            <span class="text-xs font-bold px-1.5 py-0.5 rounded-md
                         {{ $gender === 'female' ? 'bg-pink-100 text-pink-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $femaleCount }}
            </span>
        </a>
    </div>
</div>

{{-- Search & Class Filter --}}
<div class="bg-white p-2 rounded-2xl border border-gray-200 mb-5 shadow-sm">
    <form method="GET" action="{{ route('teacher.students.index') }}" 
          class="flex flex-col sm:flex-row gap-2">

        {{-- Preserve gender filter --}}
        @if ($gender)
            <input type="hidden" name="gender" value="{{ $gender }}">
        @endif

        {{-- Search --}}
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="ti ti-search text-gray-400"></i>
            </div>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by name or ID..."
                   class="w-full bg-gray-50 border border-transparent focus:bg-white 
                          focus:border-green-500 focus:ring-2 focus:ring-green-200 
                          rounded-xl pl-10 pr-4 py-2.5 text-sm text-gray-700 
                          transition-all placeholder-gray-400">
        </div>

        {{-- Class Filter (only if teacher has 2+ classes) --}}
        @if ($classes->count() > 1)
            <div class="relative sm:min-w-56">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="ti ti-building text-gray-400"></i>
                </div>
                <select name="class_id"
                        class="w-full bg-gray-50 border border-transparent focus:bg-white 
                               focus:border-green-500 focus:ring-2 focus:ring-green-200 
                               rounded-xl pl-10 pr-8 py-2.5 text-sm text-gray-700 
                               transition-all appearance-none cursor-pointer">
                    <option value="">— All Classes —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} ({{ $class->grade->name }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <button type="submit"
                class="px-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 
                       text-sm font-semibold rounded-xl border border-gray-200 
                       transition-colors whitespace-nowrap">
            Search
        </button>

        @if ($search || $classId)
            <a href="{{ route('teacher.students.index', ['gender' => $gender]) }}" 
               title="Clear filters"
               class="px-3 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 
                      text-sm font-semibold rounded-xl border border-red-100 
                      transition-colors flex items-center justify-center">
                <i class="ti ti-x text-lg"></i>
            </a>
        @endif
    </form>
</div>

{{-- Student List --}}
@if ($students->isNotEmpty())
    <div class="space-y-2 mb-4">
        @foreach ($students as $student)
            @php
                $enrollment = $student->enrollments
                    ->whereIn('class_id', $classes->pluck('id'))
                    ->sortByDesc('enrolled_at')
                    ->first();
            @endphp

            <a href="{{ route('teacher.students.show', $student) }}"
               class="flex items-center gap-3 bg-white border border-gray-200 
                      rounded-xl p-3 hover:border-green-200 hover:shadow-sm 
                      transition-all">

                {{-- Avatar --}}
                <div class="w-12 h-12 rounded-xl overflow-hidden flex items-center 
                            justify-center font-bold shadow-inner flex-shrink-0
                            {{ $student->gender === 'female' 
                                ? 'bg-gradient-to-br from-pink-100 to-rose-100 text-pink-700' 
                                : 'bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700' }}">
                    @if ($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" 
                             alt="{{ $student->full_name }}"
                             class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                    @endif
                </div>

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-800 truncate">
                        {{ $student->full_name }}
                    </p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-[10px] font-mono text-gray-400">
                            {{ $student->student_id }}
                        </span>
                        @if ($enrollment)
                            <span class="text-gray-300">·</span>
                            <span class="text-[10px] text-gray-500 truncate">
                                {{ $enrollment->schoolClass->name }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Gender Badge --}}
                @if ($student->gender)
                    <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 
                                 rounded-md text-[10px] font-semibold
                                 {{ $student->gender === 'female' 
                                     ? 'bg-pink-50 text-pink-600' 
                                     : 'bg-blue-50 text-blue-600' }}">
                        <i class="ti {{ $student->gender === 'female' 
                                        ? 'ti-gender-female' 
                                        : 'ti-gender-male' }} text-[10px]"></i>
                        {{ $student->gender === 'female' ? 'ស្រី' : 'ប្រុស' }}
                    </span>
                @endif

                <i class="ti ti-chevron-right text-gray-300 flex-shrink-0"></i>
            </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    @if ($students->hasPages())
        <div class="bg-white border border-gray-200 rounded-xl px-4 py-3">
            {{ $students->links() }}
        </div>
    @endif

@else
    {{-- Empty State --}}
    <div class="bg-white border border-gray-200 rounded-2xl px-5 py-16 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center 
                    mx-auto mb-4 border border-gray-100">
            <i class="ti ti-users-off text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-sm font-bold text-gray-800 mb-1">No students found</h3>
        <p class="text-sm text-gray-500 mb-4">
            @if($search || $classId)
                No students match your current filters.
            @else
                No students in your assigned classes yet.
            @endif
        </p>
        @if($search || $classId)
            <a href="{{ route('teacher.students.index') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 
                      rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="ti ti-x text-sm"></i>
                Clear Filters
            </a>
        @endif
    </div>
@endif

@endsection