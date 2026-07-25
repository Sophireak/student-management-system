@extends('layouts.admin', ['title' => $student->full_name])

@section('content')

{{-- Back + Actions --}}
<div class="flex items-center justify-between mb-6">
    <a href="{{ route('admin.students.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 
              hover:text-gray-700 transition-colors group">
        <i class="ti ti-arrow-left text-base group-hover:-translate-x-0.5 transition-transform"></i>
        Back to Students
    </a>
    <div class="flex items-center gap-2">
        <a href="{{ route('admin.students.edit', $student) }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200
                  text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 
                  transition-all active:scale-[0.98]">
            <i class="ti ti-pencil text-lg"></i>
            Edit Student
        </a>
    </div>
</div>

{{-- Profile Hero Card --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden mb-5">

    {{-- Banner --}}
    <div class="h-28 relative
                {{ $student->gender === 'female' 
                    ? 'bg-gradient-to-r from-pink-500 to-rose-400' 
                    : 'bg-gradient-to-r from-blue-600 to-indigo-500' }}">
    </div>

    {{-- Profile Content --}}
    <div class="relative px-6 pb-6">

        {{-- Avatar --}}
        <div class="-mt-10 mb-4 flex items-end justify-between">
            <div class="w-20 h-20 rounded-2xl flex items-center justify-center 
                        font-extrabold text-3xl shadow-lg ring-4 ring-white flex-shrink-0
                        {{ $student->gender === 'female' 
                            ? 'bg-gradient-to-br from-pink-100 to-rose-100 text-pink-700' 
                            : 'bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700' }}">
                {{ strtoupper(substr($student->first_name, 0, 1)) }}
            </div>
            <div class="flex items-center gap-2 pb-1">
                @php
                    $activeEnrollment = $student->enrollments->where('status', 'active')->first();
                @endphp
                @if($activeEnrollment)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs 
                                 font-bold bg-green-50 text-green-700 border border-green-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Active Student
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs 
                                 font-bold bg-gray-50 text-gray-500 border border-gray-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                        Not Enrolled
                    </span>
                @endif
            </div>
        </div>

        {{-- Name & Info --}}
        <div>
            <h1 class="text-2xl font-extrabold text-gray-800 tracking-tight">
                {{ $student->full_name }}
            </h1>
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-xs 
                             font-mono font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                    <i class="ti ti-id-badge text-gray-400"></i>
                    {{ $student->student_id }}
                </span>
                @if($student->gender)
                    <span class="text-gray-300">·</span>
                    <span class="flex items-center gap-1.5 text-sm text-gray-500 capitalize">
                        <i class="ti {{ $student->gender === 'female' ? 'ti-gender-female' : 'ti-gender-male' }} text-gray-400 text-xs"></i>
                        {{ $student->gender }}
                    </span>
                @endif
                @if($student->date_of_birth)
                    <span class="text-gray-300">·</span>
                    <span class="flex items-center gap-1.5 text-sm text-gray-500">
                        <i class="ti ti-cake text-gray-400 text-xs"></i>
                        {{ $student->date_of_birth->format('M d, Y') }}
                    </span>
                @endif
                @if($activeEnrollment)
                    <span class="text-gray-300">·</span>
                    <span class="flex items-center gap-1.5 text-sm text-gray-500">
                        <i class="ti ti-building text-gray-400 text-xs"></i>
                        {{ $activeEnrollment->schoolClass->name }}
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
            <i class="ti ti-clipboard-list text-green-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ $student->enrollments->count() }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Enrollments</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-building text-blue-500 text-lg"></i>
        </div>
        <p class="text-2xl font-extrabold text-gray-800">
            {{ $activeEnrollment ? $activeEnrollment->schoolClass->name : '—' }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Current Class</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-award text-purple-500 text-lg"></i>
        </div>
        <p class="text-lg font-extrabold text-gray-800">
            {{ $activeEnrollment ? $activeEnrollment->schoolClass->grade->name : '—' }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Grade</p>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 p-4 text-center">
        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center mx-auto mb-2">
            <i class="ti ti-calendar text-amber-500 text-lg"></i>
        </div>
        <p class="text-lg font-extrabold text-gray-800">
            {{ $activeEnrollment ? $activeEnrollment->schoolClass->academicYear->name : '—' }}
        </p>
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wide mt-0.5">Year</p>
    </div>

</div>

{{-- Main Content Grid --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Personal + Guardian Details --}}
    <div class="space-y-5">

        {{-- Personal Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center">
                    <i class="ti ti-user-circle text-blue-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Personal Details</h2>
            </div>
            <div class="p-5 space-y-4">
                @php
                    $personalDetails = [
                        ['icon' => 'ti-id-badge', 'label' => 'Student ID', 'value' => $student->student_id, 'mono' => true],
                        ['icon' => 'ti-user', 'label' => 'Full Name', 'value' => $student->full_name],
                        ['icon' => 'ti-gender-bigender', 'label' => 'Gender', 'value' => $student->gender ? ucfirst($student->gender) : null],
                        ['icon' => 'ti-cake', 'label' => 'Date of Birth', 'value' => $student->date_of_birth?->format('M d, Y')],
                        ['icon' => 'ti-phone', 'label' => 'Phone', 'value' => $student->phone ?? null],
                        ['icon' => 'ti-map-pin', 'label' => 'Address', 'value' => $student->address ?? null],
                    ];
                @endphp

                @foreach ($personalDetails as $detail)
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

        {{-- Guardian Info --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center">
                    <i class="ti ti-user-heart text-purple-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Guardian Information</h2>
            </div>
            <div class="p-5 space-y-4">
                @php
                    $guardianDetails = [
                        ['icon' => 'ti-users', 'label' => 'Guardian Name', 'value' => $student->guardian_name ?? null],
                        ['icon' => 'ti-phone', 'label' => 'Guardian Phone', 'value' => $student->guardian_phone ?? null],
                        ['icon' => 'ti-heart', 'label' => 'Relationship', 'value' => $student->guardian_relationship ? ucfirst($student->guardian_relationship) : null],
                    ];
                @endphp

                @foreach ($guardianDetails as $detail)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center 
                                    justify-center flex-shrink-0 border border-gray-100">
                            <i class="ti {{ $detail['icon'] }} text-gray-400 text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                {{ $detail['label'] }}
                            </p>
                            <p class="text-sm font-medium text-gray-700 mt-0.5">
                                {{ $detail['value'] ?? '—' }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Right: Enrollment History --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
                    <i class="ti ti-clipboard-list text-green-500 text-sm"></i>
                </div>
                <h2 class="text-sm font-bold text-gray-700">Enrollment History</h2>
            </div>
            <span class="px-2.5 py-1 text-xs font-bold rounded-full bg-gray-100 text-gray-600">
                {{ $student->enrollments->count() }} total
            </span>
        </div>

        @if($student->enrollments->isNotEmpty())
            <div class="divide-y divide-gray-100">
                @foreach ($student->enrollments->sortByDesc('created_at') as $enrollment)
                    @php
                        $statusConfig = match($enrollment->status) {
                            'active'      => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-100', 'dot' => 'bg-green-500'],
                            'transferred' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'dot' => 'bg-blue-500'],
                            'dropped'     => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-100', 'dot' => 'bg-red-500'],
                            default       => ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400'],
                        };
                    @endphp

                    <div class="px-5 py-4 hover:bg-gray-50/50 transition-colors" 
                         x-data="{ showActions: false }">

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br 
                                            from-purple-100 to-indigo-100 text-purple-700 
                                            flex items-center justify-center font-bold flex-shrink-0">
                                    {{ strtoupper(substr($enrollment->schoolClass->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $enrollment->schoolClass->name }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-0.5">
                                        <span class="text-xs text-gray-400">
                                            {{ $enrollment->schoolClass->grade->name }}
                                        </span>
                                        <span class="text-gray-300">·</span>
                                        <span class="text-xs text-gray-400">
                                            {{ $enrollment->schoolClass->academicYear->name }}
                                        </span>
                                        @if($enrollment->enrolled_at)
                                            <span class="text-gray-300">·</span>
                                            <span class="text-xs text-gray-400">
                                                {{ $enrollment->enrolled_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-bold border
                                             {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} {{ $statusConfig['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                                    {{ ucfirst($enrollment->status) }}
                                </span>

                                @if($enrollment->status === 'active')
                                    <button @click="showActions = !showActions"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center 
                                                   text-gray-400 hover:text-gray-600 hover:bg-gray-100 
                                                   transition-colors">
                                        <i class="ti ti-dots-vertical text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        </div>

                        {{-- Action Buttons (Alpine toggle) --}}
                        @if($enrollment->status === 'active')
                            <div x-show="showActions" x-cloak
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 class="flex gap-2 mt-3 pt-3 border-t border-gray-100">

                                <button onclick="openModal('transfer-{{ $enrollment->id }}')"
                                        class="inline-flex items-center gap-1.5 text-xs px-3 py-2 
                                               bg-blue-50 border border-blue-200 text-blue-700 
                                               rounded-xl hover:bg-blue-100 transition-colors 
                                               font-semibold active:scale-[0.98]">
                                    <i class="ti ti-transfer-in text-sm"></i> Transfer
                                </button>

                                <button onclick="openModal('promote-{{ $enrollment->id }}')"
                                        class="inline-flex items-center gap-1.5 text-xs px-3 py-2 
                                               bg-purple-50 border border-purple-200 text-purple-700 
                                               rounded-xl hover:bg-purple-100 transition-colors 
                                               font-semibold active:scale-[0.98]">
                                    <i class="ti ti-arrow-up text-sm"></i> Promote
                                </button>
                            </div>

                            {{-- Transfer Modal --}}
                            <div id="transfer-{{ $enrollment->id }}"
                                 class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 
                                        flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 
                                            w-full max-w-sm overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center">
                                            <i class="ti ti-transfer-in text-blue-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-800">Transfer Student</h4>
                                            <p class="text-xs text-gray-400">Move to a different class in the same year</p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.enrollments.transfer', $enrollment) }}">
                                        @csrf
                                        <div class="p-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                                New Class 
                                                <span class="text-xs font-normal text-gray-400">
                                                    ({{ $enrollment->schoolClass->academicYear->name }})
                                                </span>
                                            </label>
                                            <select name="class_id" required
                                                    class="w-full border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm
                                                           focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100
                                                           transition-all">
                                                <option value="">Select class</option>
                                                @foreach ($sameYearClasses[$enrollment->id] ?? [] as $cls)
                                                    <option value="{{ $cls->id }}">
                                                        {{ $cls->name }} ({{ $cls->grade->name }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 
                                                    flex gap-2 justify-end">
                                            <button type="button" 
                                                    onclick="closeModal('transfer-{{ $enrollment->id }}')"
                                                    class="px-4 py-2 text-sm font-semibold text-gray-600 
                                                           bg-white border border-gray-200 rounded-xl 
                                                           hover:bg-gray-50 transition-colors">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                    class="px-4 py-2 text-sm font-semibold text-white 
                                                           bg-blue-600 rounded-xl hover:bg-blue-700 
                                                           transition-colors active:scale-[0.98]">
                                                Confirm Transfer
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            {{-- Promote Modal --}}
                            <div id="promote-{{ $enrollment->id }}"
                                 class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 
                                        flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 
                                            w-full max-w-sm overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center">
                                            <i class="ti ti-arrow-up text-purple-600 text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-gray-800">Promote Student</h4>
                                            <p class="text-xs text-gray-400">Move to a class in the next academic year</p>
                                        </div>
                                    </div>
                                    <form method="POST" action="{{ route('admin.enrollments.promote', $enrollment) }}">
                                        @csrf
                                        <div class="p-6">
                                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                                New Class 
                                                <span class="text-xs font-normal text-gray-400">(Next Year)</span>
                                            </label>
                                            <select name="class_id" required
                                                    class="w-full border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm
                                                           focus:bg-white focus:border-purple-500 focus:ring-2 focus:ring-purple-100
                                                           transition-all">
                                                <option value="">Select class</option>
                                                @foreach ($nextYearClasses as $cls)
                                                    <option value="{{ $cls->id }}">
                                                        {{ $cls->name }} ({{ $cls->grade->name }}) · {{ $cls->academicYear->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 
                                                    flex gap-2 justify-end">
                                            <button type="button" 
                                                    onclick="closeModal('promote-{{ $enrollment->id }}')"
                                                    class="px-4 py-2 text-sm font-semibold text-gray-600 
                                                           bg-white border border-gray-200 rounded-xl 
                                                           hover:bg-gray-50 transition-colors">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                    class="px-4 py-2 text-sm font-semibold text-white 
                                                           bg-purple-600 rounded-xl hover:bg-purple-700 
                                                           transition-colors active:scale-[0.98]">
                                                Confirm Promotion
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @else
            <div class="py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-gray-50 flex items-center 
                            justify-center mx-auto mb-4 border border-gray-100">
                    <i class="ti ti-clipboard-off text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-sm font-bold text-gray-800 mb-1">No enrollments yet</h3>
                <p class="text-sm text-gray-500 mb-4">
                    This student hasn't been enrolled in any class.
                </p>
                <a href="{{ route('admin.enrollments.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700 
                          rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                    <i class="ti ti-clipboard-plus"></i> Enroll Student
                </a>
            </div>
        @endif

    </div>

</div>

@push('scripts')
<script>
    function openModal(id) { 
        document.getElementById(id).classList.remove('hidden'); 
    }
    function closeModal(id) { 
        document.getElementById(id).classList.add('hidden'); 
    }
    document.querySelectorAll('[id^="transfer-"], [id^="promote-"]').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });
</script>
@endpush

@endsection