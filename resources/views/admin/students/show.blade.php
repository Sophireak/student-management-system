@extends('layouts.admin', ['title' => ''])

@section('content')

{{-- Back + Header --}}
<div class="mb-6">
    <a href="{{ url()->previous() }}"
   class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
    <i class="ti ti-arrow-left text-base"></i> Back
</a>
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-user text-green-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $student->full_name }}</h1>
                <p class="text-sm text-gray-400 font-mono mt-0.5">{{ $student->student_id }}</p>
            </div>
        </div>
        <a href="{{ route('admin.students.edit', $student) }}"
           class="flex items-center gap-2 px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg border border-yellow-200 transition-colors">
            <i class="ti ti-pencil text-base"></i> Edit
        </a>
    </div>
</div>

{{-- Alerts --}}
@if (session('success'))
    <div class="mb-4 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-circle-check text-base"></i> {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 rounded-xl">
        <i class="ti ti-alert-circle text-base"></i> {{ session('error') }}
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Student Info Card --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Student Information</h2>

        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-id-badge text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Student ID</p>
                    <p class="text-sm font-medium text-gray-700 font-mono">{{ $student->student_id }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-user text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Full Name</p>
                    <p class="text-sm font-medium text-gray-700">{{ $student->full_name }}</p>
                </div>
            </div>

            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-gender-bigender text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Gender</p>
                    <p class="text-sm font-medium text-gray-700 capitalize">{{ $student->gender ?? '—' }}</p>
                </div>
            </div>

            @if ($student->date_of_birth)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-cake text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Date of Birth</p>
                    <p class="text-sm font-medium text-gray-700">{{ $student->date_of_birth->format('M d, Y') }}</p>
                </div>
            </div>
            @endif

            <div class="pt-2 border-t border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Guardian</p>

                <div class="flex items-start gap-3 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-users text-gray-400 text-base"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Guardian Name</p>
                        <p class="text-sm font-medium text-gray-700">{{ $student->guardian_name ?? '—' }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                        <i class="ti ti-phone text-gray-400 text-base"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Guardian Phone</p>
                        <p class="text-sm font-medium text-gray-700">{{ $student->guardian_phone ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Enrollments --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Enrollment History</h2>

        @forelse ($student->enrollments()->with(['schoolClass.grade','schoolClass.academicYear'])->latest()->get() as $enrollment)
            <div class="py-4 border-b border-gray-100 last:border-0">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-gray-800 text-sm">{{ $enrollment->schoolClass->name }}</span>
                        <span class="text-gray-300">·</span>
                        <span class="text-sm text-gray-500">{{ $enrollment->schoolClass->grade->name }}</span>
                        <span class="text-gray-300">·</span>
                        <span class="text-sm text-gray-500">{{ $enrollment->schoolClass->academicYear->name }}</span>
                    </div>
                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                        {{ $enrollment->status === 'active'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($enrollment->status) }}
                    </span>
                </div>

                @if ($enrollment->status === 'active')
                    <div class="flex gap-2 mt-2">
                        <button onclick="openModal('transfer-{{ $enrollment->id }}')"
                                class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="ti ti-transfer-in text-sm"></i> Transfer
                        </button>
                        <button onclick="openModal('promote-{{ $enrollment->id }}')"
                                class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-purple-50 border border-purple-200 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                            <i class="ti ti-arrow-up text-sm"></i> Promote
                        </button>
                    </div>

                    {{-- Transfer Modal --}}
                    <div id="transfer-{{ $enrollment->id }}"
                         class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-sm p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-9 h-9 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <i class="ti ti-transfer-in text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">Transfer Student</h4>
                                    <p class="text-xs text-gray-400">Move to a different class in the same year</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.enrollments.transfer', $enrollment) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">
                                        New Class ({{ $enrollment->schoolClass->academicYear->name }})
                                    </label>
                                    <select name="class_id" required
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                   focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">— Select Class —</option>
                                        @foreach ($sameYearClasses[$enrollment->id] ?? [] as $cls)
                                            <option value="{{ $cls->id }}">{{ $cls->name }} ({{ $cls->grade->name }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 justify-end">
                                    <button type="button" onclick="closeModal('transfer-{{ $enrollment->id }}')"
                                            class="px-4 py-2 text-xs text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-xs text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                                        Confirm Transfer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Promote Modal --}}
                    <div id="promote-{{ $enrollment->id }}"
                         class="hidden fixed inset-0 bg-black bg-opacity-40 z-50 flex items-center justify-center p-4">
                        <div class="bg-white rounded-xl shadow-xl border border-gray-200 w-full max-w-sm p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-9 h-9 rounded-lg bg-purple-50 flex items-center justify-center">
                                    <i class="ti ti-arrow-up text-purple-600"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800">Promote Student</h4>
                                    <p class="text-xs text-gray-400">Move to a class in the next academic year</p>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.enrollments.promote', $enrollment) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs font-medium text-gray-600 mb-1">New Class (Next Year)</label>
                                    <select name="class_id" required
                                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                                                   focus:outline-none focus:ring-2 focus:ring-purple-500">
                                        <option value="">— Select Class —</option>
                                        @foreach ($nextYearClasses as $cls)
                                            <option value="{{ $cls->id }}">
                                                {{ $cls->name }} ({{ $cls->grade->name }}) · {{ $cls->academicYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 justify-end">
                                    <button type="button" onclick="closeModal('promote-{{ $enrollment->id }}')"
                                            class="px-4 py-2 text-xs text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-xs text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                                        Confirm Promotion
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="py-10 text-center">
                <i class="ti ti-clipboard-off text-4xl text-gray-300 block mb-2"></i>
                <p class="text-sm text-gray-400">No enrollments yet.</p>
            </div>
        @endforelse
    </div>

</div>

@push('scripts')
<script>
function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }
document.querySelectorAll('[id^="transfer-"], [id^="promote-"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>
@endpush

@endsection
