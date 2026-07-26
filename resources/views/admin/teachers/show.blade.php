@extends('layouts.admin', ['title' => $teacher->user->name])

@section('content')

{{-- Back + Actions --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.teachers.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Teachers
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.teachers.edit', $teacher) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200
                  text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 
                  transition-all active:scale-[0.98]">
            <i class="ti ti-pencil text-lg"></i>
            Edit Teacher
        </a>
    </div>
</div>

{{-- Profile Hero Card --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">

    {{-- Green Header Banner --}}
    <div class="h-28 bg-gradient-to-r from-green-600 to-emerald-500 relative"></div>

    {{-- Profile Content --}}
    <div class="relative px-6 pb-6">

        {{-- Avatar (overlaps banner) --}}
        <div class="-mt-10 mb-4 flex items-end justify-between">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-green-100 to-emerald-100 
                        text-green-700 flex items-center justify-center font-extrabold text-3xl 
                        shadow-lg ring-4 ring-white flex-shrink-0">
                {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
            </div>
            <div class="flex items-center gap-2 pb-1">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs 
                             font-bold bg-green-50 text-green-700 border border-green-100">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                    Active Teacher
                </span>
            </div>
        </div>

        {{-- Name & Basic Info --}}
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                {{ $teacher->user->name }}
            </h1>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                <span class="flex items-center gap-1.5 text-sm text-gray-500">
                    <i class="ti ti-mail text-gray-400 text-xs"></i>
                    {{ $teacher->user->email }}
                </span>
                @if($teacher->phone)
                    <span class="text-gray-300 hidden sm:inline">·</span>
                    <span class="flex items-center gap-1.5 text-sm text-gray-500">
                        <i class="ti ti-phone text-gray-400 text-xs"></i>
                        {{ $teacher->phone }}
                    </span>
                @endif
                @if($teacher->employee_id)
                    <span class="text-gray-300 hidden sm:inline">·</span>
                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs 
                                 font-mono font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                        <i class="ti ti-id-badge text-gray-400"></i>
                        {{ $teacher->employee_id }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-building text-green-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ $teacher->classes->count() }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Classes</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-users text-blue-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ $teacher->classes->sum('enrollments_count') }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Students</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-calendar text-purple-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ $teacher->created_at->format('Y') }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Joined</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-gender-bigender text-amber-500 text-lg"></i>
        </div>
        <p class="text-lg font-extrabold text-gray-800 capitalize">
            {{ $teacher->gender ?? 'N/A' }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Gender</p>
    </div>

</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Personal Details --}}
    <div class="space-y-5">

        {{-- Personal Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="ti ti-user-circle text-green-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Personal Details</h2>
            </div>
            <div class="p-5 space-y-4">

                @php
                    $details = [
                        ['icon' => 'ti-mail', 'label' => 'Email', 'value' => $teacher->user->email],
                        ['icon' => 'ti-phone', 'label' => 'Phone', 'value' => $teacher->phone ?? null],
                        ['icon' => 'ti-id-badge', 'label' => 'Employee ID', 'value' => $teacher->employee_id ?? null, 'mono' => true],
                        ['icon' => 'ti-gender-bigender', 'label' => 'Gender', 'value' => $teacher->gender ? ucfirst($teacher->gender) : null],
                        ['icon' => 'ti-cake', 'label' => 'Date of Birth', 'value' => $teacher->date_of_birth?->format('M d, Y') ?? null],
                        ['icon' => 'ti-map-pin', 'label' => 'Address', 'value' => $teacher->address ?? null],
                    ];
                @endphp

                @foreach ($details as $detail)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center 
                                    justify-center flex-shrink-0 border border-gray-100">
                            <i class="ti {{ $detail['icon'] }} text-gray-400 text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                {{ $detail['label'] }}
                            </p>
                            <p class="text-sm font-medium text-gray-700 mt-0.5 
                                      {{ isset($detail['mono']) && $detail['mono'] ? 'font-mono' : '' }}">
                                {{ $detail['value'] ?? '—' }}
                            </p>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

        {{-- Account Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-lock text-blue-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Account Details</h2>
            </div>
            <div class="p-5 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center 
                                justify-center flex-shrink-0 border border-gray-100">
                        <i class="ti ti-calendar-plus text-gray-400 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                            Registered On
                        </p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">
                            {{ $teacher->created_at->format('M d, Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center 
                                justify-center flex-shrink-0 border border-gray-100">
                        <i class="ti ti-refresh text-gray-400 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                            Last Updated
                        </p>
                        <p class="text-sm font-medium text-gray-700 mt-0.5">
                            {{ $teacher->updated_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Right: Assigned Classes --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="ti ti-building text-purple-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Assigned Classes</h2>
            </div>
            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">
                {{ $teacher->classes->count() }} total
            </span>
        </div>

        @if($teacher->classes->isNotEmpty())
            <div class="divide-y divide-gray-100">
                @foreach ($teacher->classes as $class)
                    <div class="flex items-center justify-between px-5 py-4 
                                hover:bg-gray-50/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br 
                                        from-purple-100 to-indigo-100 text-purple-700 
                                        flex items-center justify-center font-bold flex-shrink-0">
                                {{ strtoupper(substr($class->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-800">
                                    {{ $class->name }}
                                </p>
                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                                    <span class="text-xs text-gray-400">
                                        {{ $class->grade->name }}
                                    </span>
                                    <span class="text-gray-300">·</span>
                                    <span class="text-xs text-gray-400">
                                        {{ $class->academicYear->name }}
                                    </span>
                                    <span class="text-gray-300">·</span>
                                    <span class="text-xs text-gray-400">
                                        {{ $class->enrollments_count }} students
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($class->pivot->is_primary ?? false)
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
                            <a href="{{ route('admin.classes.show', $class) }}"
                               class="w-7 h-7 rounded-lg flex items-center justify-center 
                                      text-gray-400 hover:text-blue-600 hover:bg-blue-50 
                                      transition-colors opacity-0 group-hover:opacity-100">
                                <i class="ti ti-arrow-right text-sm"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center 
                            justify-center mx-auto mb-4 border border-gray-100">
                    <i class="ti ti-building-off text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-800 mb-1">No classes assigned</h3>
                <p class="text-sm text-gray-500 mb-4">
                    This teacher hasn't been assigned to any class yet.
                </p>
                <a href="{{ route('admin.teachers.edit', $teacher) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 
                          rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                    <i class="ti ti-building-plus"></i> Assign Class
                </a>
            </div>
        @endif

    </div>

</div>

@endsection