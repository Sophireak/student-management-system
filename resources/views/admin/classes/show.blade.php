@extends('layouts.admin', ['title' => $class->name])

@section('content')

{{-- Back + Actions --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.classes.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Classes
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.classes.teachers.create', $class) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700
                  text-white text-sm font-semibold rounded-xl transition-all shadow-sm
                  hover:shadow-green-500/20 active:scale-[0.98]">
            <i class="ti ti-user-plus text-lg"></i>
            Assign Teacher
        </a>
        <a href="{{ route('admin.classes.edit', $class) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200
                  text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 
                  transition-all active:scale-[0.98]">
            <i class="ti ti-pencil text-lg"></i>
            Edit
        </a>
    </div>
</div>

{{-- Profile Hero Card --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
    <div class="h-28 bg-gradient-to-r from-purple-600 to-indigo-500 relative"></div>
    <div class="relative px-6 pb-6">
        <div class="-mt-10 mb-4 flex items-end justify-between">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-purple-100 to-indigo-100 
                        text-purple-700 flex items-center justify-center font-extrabold text-3xl 
                        shadow-lg ring-4 ring-white flex-shrink-0">
                {{ strtoupper(substr($class->name, 0, 1)) }}
            </div>
            <div class="flex items-center gap-2 pb-1">
                @if($class->academicYear->is_active)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs 
                                 font-bold bg-green-50 text-green-700 border border-green-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Active Year
                    </span>
                @endif
            </div>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                {{ $class->name }}
            </h1>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                             font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                    <i class="ti ti-award text-indigo-400"></i>
                    {{ $class->grade->name }}
                </span>
                <span class="text-gray-300">·</span>
                <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                    <i class="ti ti-calendar text-gray-400 text-xs"></i>
                    {{ $class->academicYear->name }}
                </span>
                @if($class->capacity)
                    <span class="text-gray-300">·</span>
                    <span class="inline-flex items-center gap-1.5 text-sm text-gray-500">
                        <i class="ti ti-users text-gray-400 text-xs"></i>
                        {{ $class->capacity }} seats
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Stats Row --}}
@php
    $activeStudents = $class->enrollments->where('status', 'active')->count();
    $totalStudents = $class->enrollments->count();
    $fillPercent = $class->capacity > 0 ? round(($activeStudents / $class->capacity) * 100) : 0;
    $fillColor = $fillPercent >= 90 ? 'red' : ($fillPercent >= 70 ? 'amber' : 'green');
@endphp

<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-users text-green-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">{{ $activeStudents }}</p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Active</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-clipboard-list text-blue-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">{{ $totalStudents }}</p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Total</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-school text-purple-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ $class->teachers->count() }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Teachers</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-chart-bar text-amber-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ $class->capacity ? $fillPercent . '%' : '∞' }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Capacity</p>
    </div>

</div>

{{-- Two Column: Teachers + Class Info --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">

    {{-- Class Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class="ti ti-info-circle text-purple-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Class Details</h2>
        </div>
        <div class="p-5 space-y-4">
            @php
                $classDetails = [
                    ['icon' => 'ti-building', 'label' => 'Class Name', 'value' => $class->name],
                    ['icon' => 'ti-award', 'label' => 'Grade', 'value' => $class->grade->name],
                    ['icon' => 'ti-calendar', 'label' => 'Academic Year', 'value' => $class->academicYear->name],
                    ['icon' => 'ti-users', 'label' => 'Capacity', 'value' => $class->capacity ? $class->capacity . ' seats' : 'Unlimited'],
                ];
            @endphp
            @foreach ($classDetails as $detail)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center 
                                justify-center flex-shrink-0 border border-gray-100">
                        <i class="ti {{ $detail['icon'] }} text-gray-400 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                            {{ $detail['label'] }}
                        </p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">
                            {{ $detail['value'] }}
                        </p>
                    </div>
                </div>
            @endforeach

            {{-- Capacity Bar --}}
            @if($class->capacity > 0)
                <div class="pt-2 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-medium text-gray-500">Fill Rate</span>
                        <span class="text-xs font-bold text-{{ $fillColor }}-600">
                            {{ $activeStudents }}/{{ $class->capacity }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full bg-{{ $fillColor }}-500 transition-all"
                             style="width: {{ min($fillPercent, 100) }}%">
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Assigned Teachers --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="ti ti-school text-green-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Assigned Teachers</h2>
            </div>
            <a href="{{ route('admin.classes.teachers.create', $class) }}"
               class="text-xs font-semibold text-green-600 hover:text-green-700 
                      flex items-center gap-1 transition-colors">
                <i class="ti ti-plus text-sm"></i> Add
            </a>
        </div>

        @php
            $classTeachers = $class->classTeachers()->with('teacher.user')->get();
        @endphp

        @if($classTeachers->isNotEmpty())
            <div class="divide-y divide-gray-100">
                @foreach ($classTeachers as $ct)
                    <div class="flex items-center justify-between px-5 py-4 
                                hover:bg-gray-50/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br 
                                        from-green-100 to-emerald-100 text-green-700 
                                        flex items-center justify-center font-bold flex-shrink-0">
                                {{ strtoupper(substr($ct->teacher->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ $ct->teacher->user->name }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ $ct->teacher->user->email }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($ct->is_primary)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 
                                             rounded-lg text-xs font-bold 
                                             bg-green-50 text-green-700 border border-green-100">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Primary
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 
                                             rounded-lg text-xs font-bold 
                                             bg-gray-50 text-gray-500 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                    Assistant
                                </span>
                            @endif
                            <form method="POST" 
                                  action="{{ route('admin.classes.teachers.destroy', [$class, $ct]) }}"
                                  onsubmit="return confirm('Remove {{ $ct->teacher->user->name }} from this class?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="w-7 h-7 rounded-lg flex items-center justify-center 
                                               text-gray-400 hover:text-red-600 hover:bg-red-50 
                                               transition-colors opacity-0 group-hover:opacity-100"
                                        title="Remove teacher">
                                    <i class="ti ti-x text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center 
                            justify-center mx-auto mb-3 border border-gray-100">
                    <i class="ti ti-user-off text-xl text-gray-400"></i>
                </div>
                <p class="text-sm font-bold text-gray-800 mb-1">No teachers assigned</p>
                <p class="text-xs text-gray-400 mb-3">Assign a teacher to this class</p>
                <a href="{{ route('admin.classes.teachers.create', $class) }}"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-green-50 text-green-700 
                          rounded-lg text-xs font-bold hover:bg-green-100 transition-colors">
                    <i class="ti ti-user-plus"></i> Assign Teacher
                </a>
            </div>
        @endif
    </div>

</div>

{{-- Enrolled Students Table --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ti ti-users text-blue-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Enrolled Students</h2>
            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">
                {{ $activeStudents }} active
            </span>
        </div>
        <a href="{{ route('admin.enrollments.create') }}"
           class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-600 
                  hover:text-green-700 transition-colors">
            <i class="ti ti-user-plus text-sm"></i> Enroll Student
        </a>
    </div>

    @if($class->enrollments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-200">
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest">#</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest">Student</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest hidden sm:table-cell">Student ID</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest hidden md:table-cell">Gender</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/80">
                    @foreach ($class->enrollments->sortBy('student.last_name')->values() as $i => $enrollment)
                        @php
                            $statusConfig = match($enrollment->status) {
                                'active'      => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-100', 'dot' => 'bg-green-500'],
                                'transferred' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'dot' => 'bg-blue-500'],
                                'dropped'     => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'dot' => 'bg-red-500'],
                                default       => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-3 text-xs font-medium text-gray-400">
                                {{ $i + 1 }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center 
                                                font-bold text-sm flex-shrink-0
                                                {{ $enrollment->student?->gender === 'female' 
                                                    ? 'bg-gradient-to-br from-pink-100 to-rose-100 text-pink-700' 
                                                    : 'bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700' }}">
                                        {{ strtoupper(substr($enrollment->student?->first_name ?? '?', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-800">
                                        {{ $enrollment->student?->full_name ?? '—' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-5 py-3 hidden sm:table-cell">
                                <span class="text-xs font-mono font-medium text-gray-400">
                                    {{ $enrollment->student?->student_id ?? '—' }}
                                </span>
                            </td>
                            <td class="px-5 py-3 hidden md:table-cell">
                                @if($enrollment->student?->gender)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-semibold
                                                 {{ $enrollment->student->gender === 'female' 
                                                     ? 'bg-pink-50 text-pink-600 border border-pink-100' 
                                                     : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                        {{ ucfirst($enrollment->student->gender) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-bold border
                                             {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                @if($enrollment->student && !$enrollment->student->trashed())
                                    <a href="{{ route('admin.students.show', $enrollment->student) }}"
                                       class="w-8 h-8 rounded-lg inline-flex items-center justify-center 
                                              text-gray-400 bg-gray-50 hover:bg-blue-50 hover:text-blue-600 
                                              transition-all border border-gray-100 hover:border-blue-100"
                                       title="View Student">
                                        <i class="ti ti-eye text-lg"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center 
                        justify-center mx-auto mb-4 border border-gray-100">
                <i class="ti ti-users-off text-2xl text-gray-400"></i>
            </div>
            <h3 class="text-sm font-bold text-gray-800 mb-1">No students enrolled</h3>
            <p class="text-sm text-gray-500 mb-4">Start enrolling students into this class.</p>
            <a href="{{ route('admin.enrollments.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 
                      rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                <i class="ti ti-user-plus"></i> Enroll First Student
            </a>
        </div>
    @endif
</div>

@endsection