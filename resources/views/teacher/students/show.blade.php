@extends('layouts.teacher', ['title' => $student->full_name])

@section('content')

{{-- Back Link --}}
<a href="{{ route('teacher.students.index') }}"
   class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="ti ti-arrow-left text-base"></i> Back to Students
</a>

{{-- Profile Header --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">
    <div class="p-5 flex flex-col sm:flex-row items-start gap-4">

        {{-- Photo/Avatar --}}
        <div class="w-24 h-32 rounded-xl overflow-hidden flex items-center justify-center 
                    font-bold text-3xl shadow-inner flex-shrink-0
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
            <h1 class="text-xl font-bold text-gray-800">{{ $student->full_name }}</h1>

            <div class="flex flex-wrap items-center gap-2 mt-2">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                             font-mono font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                    <i class="ti ti-id-badge text-gray-400"></i>
                    {{ $student->student_id }}
                </span>

                @if ($student->gender)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold
                                 {{ $student->gender === 'female' 
                                     ? 'bg-pink-50 text-pink-600 border border-pink-100' 
                                     : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                        <i class="ti {{ $student->gender === 'female' ? 'ti-gender-female' : 'ti-gender-male' }} text-xs"></i>
                        {{ $student->gender === 'female' ? 'ស្រី' : 'ប្រុស' }}
                    </span>
                @endif

                @if ($student->date_of_birth)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                 font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                        <i class="ti ti-cake text-gray-400"></i>
                        {{ $student->date_of_birth->age }} years
                    </span>
                @endif
            </div>
        </div>

        {{-- Edit Button --}}
        <a href="{{ route('teacher.students.edit', $student) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700
                  text-white text-sm font-semibold rounded-xl transition-colors active:scale-[0.98]">
            <i class="ti ti-pencil text-base"></i> Edit
        </a>
    </div>
</div>

{{-- Info Cards --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">

    {{-- Personal Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                <i class="ti ti-user text-blue-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Personal Information</h2>
        </div>
        <dl class="p-5 space-y-3">
            <div class="flex items-center justify-between">
                <dt class="text-xs text-gray-400">Date of Birth</dt>
                <dd class="text-sm font-medium text-gray-700">
                    {{ $student->date_of_birth?->format('d M Y') ?? '—' }}
                </dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-xs text-gray-400">Phone</dt>
                <dd class="text-sm text-gray-700">
                    @if ($student->phone)
                        <a href="tel:{{ $student->phone }}" class="text-green-600 hover:underline">
                            {{ $student->phone }}
                        </a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div class="flex items-start justify-between gap-3">
                <dt class="text-xs text-gray-400 flex-shrink-0">Address</dt>
                <dd class="text-sm text-gray-700 text-right">
                    {{ $student->address ?? '—' }}
                </dd>
            </div>
        </dl>
    </div>

    {{-- Guardian Info --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
            <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                <i class="ti ti-user-heart text-purple-500 text-sm"></i>
            </div>
            <h2 class="text-sm font-bold text-gray-700">Guardian Information</h2>
        </div>
        <dl class="p-5 space-y-3">
            <div class="flex items-center justify-between">
                <dt class="text-xs text-gray-400">Name</dt>
                <dd class="text-sm font-medium text-gray-700">
                    {{ $student->guardian_name ?? '—' }}
                </dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-xs text-gray-400">Phone</dt>
                <dd class="text-sm text-gray-700">
                    @if ($student->guardian_phone)
                        <a href="tel:{{ $student->guardian_phone }}" class="text-green-600 hover:underline">
                            {{ $student->guardian_phone }}
                        </a>
                    @else
                        —
                    @endif
                </dd>
            </div>
            <div class="flex items-center justify-between">
                <dt class="text-xs text-gray-400">Relationship</dt>
                <dd class="text-sm text-gray-700 capitalize">
                    {{ $student->guardian_relationship ?? '—' }}
                </dd>
            </div>
        </dl>
    </div>

</div>

{{-- Enrollment History --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100 flex items-center gap-2">
        <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
            <i class="ti ti-history text-green-500 text-sm"></i>
        </div>
        <h2 class="text-sm font-bold text-gray-700">Enrollment History</h2>
    </div>

    @if ($student->enrollments->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50/50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Class</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider hidden sm:table-cell">Grade</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider hidden md:table-cell">Academic Year</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider hidden lg:table-cell">Enrolled</th>
                        <th class="text-right px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($student->enrollments->sortByDesc('enrolled_at') as $enrollment)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3 font-medium text-gray-800">
                                {{ $enrollment->schoolClass->name }}
                                <span class="sm:hidden text-xs text-gray-400 block">
                                    {{ $enrollment->schoolClass->grade->name }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-600 hidden sm:table-cell">
                                {{ $enrollment->schoolClass->grade->name }}
                            </td>
                            <td class="px-5 py-3 text-gray-600 hidden md:table-cell">
                                {{ $enrollment->schoolClass->academicYear->name }}
                            </td>
                            <td class="px-5 py-3 text-gray-600 hidden lg:table-cell">
                                {{ $enrollment->enrolled_at?->format('d M Y') ?? '—' }}
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-semibold
                                    {{ $enrollment->status === 'active'
                                        ? 'bg-green-50 text-green-700 border border-green-100'
                                        : ($enrollment->status === 'transferred'
                                            ? 'bg-blue-50 text-blue-700 border border-blue-100'
                                            : 'bg-gray-100 text-gray-500 border border-gray-200') }}">
                                    <span class="w-1.5 h-1.5 rounded-full 
                                        {{ $enrollment->status === 'active' ? 'bg-green-500' 
                                            : ($enrollment->status === 'transferred' ? 'bg-blue-500' : 'bg-gray-400') }}">
                                    </span>
                                    {{ ucfirst($enrollment->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="px-5 py-12 text-center">
            <i class="ti ti-clipboard-off text-3xl text-gray-300 block mb-2"></i>
            <p class="text-sm text-gray-400">No enrollment records.</p>
        </div>
    @endif
</div>

@endsection