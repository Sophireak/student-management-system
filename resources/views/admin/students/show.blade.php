@extends('layouts.admin', ['title' => $student->full_name])

@section('content')

<div class="max-w-2xl">
    <a href="{{ route('admin.students.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 mb-4 inline-block">
        ← Back to Students
    </a>

    {{-- Profile card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $student->full_name }}</h2>
                <p class="font-mono text-xs text-gray-400 mt-0.5">{{ $student->student_id }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.students.edit', $student) }}"
                   class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                    Edit
                </a>
                <a href="{{ route('admin.score-report.index') }}"
                   class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                    Score Report
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Date of Birth</p>
                <p class="font-medium text-gray-800">
                    {{ $student->date_of_birth?->format('M d, Y') ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Gender</p>
                <p class="font-medium text-gray-800 capitalize">{{ $student->gender ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Phone</p>
                <p class="font-medium text-gray-800">{{ $student->phone ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Address</p>
                <p class="font-medium text-gray-800">{{ $student->address ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Guardian card --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Guardian Information</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Name</p>
                <p class="font-medium text-gray-800">{{ $student->guardian_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Relationship</p>
                <p class="font-medium text-gray-800 capitalize">
                    {{ $student->guardian_relationship ?? '—' }}
                </p>
            </div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Phone</p>
                <p class="font-medium text-gray-800">{{ $student->guardian_phone ?? '—' }}</p>
            </div>
        </div>
    </div>

    {{-- Enrollment history --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-semibold text-gray-700">Enrollment History</h3>
            <a href="{{ route('admin.enrollments.create') }}?student_id={{ $student->id }}"
               class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200">
                + New Enrollment
            </a>
        </div>

        @if (session('success'))
            <div class="mb-3 px-3 py-2 bg-green-50 border border-green-200 rounded text-sm text-green-700">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-3 px-3 py-2 bg-red-50 border border-red-200 rounded text-sm text-red-700">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @forelse ($student->enrollments()->with(['schoolClass.grade','schoolClass.academicYear'])->latest()->get() as $enrollment)
            <div class="py-3 border-b border-gray-100 last:border-0">

                {{-- Enrollment info row --}}
                <div class="flex items-center justify-between text-sm mb-2">
                    <div>
                        <span class="font-medium text-gray-800">
                            {{ $enrollment->schoolClass->name }}
                        </span>
                        <span class="text-gray-400 mx-1">·</span>
                        <span class="text-gray-500">{{ $enrollment->schoolClass->grade->name }}</span>
                        <span class="text-gray-400 mx-1">·</span>
                        <span class="text-gray-500">{{ $enrollment->schoolClass->academicYear->name }}</span>
                    </div>
                    <span class="px-2 py-0.5 text-xs rounded-full
                        {{ $enrollment->status === 'active'
                            ? 'bg-green-100 text-green-700'
                            : 'bg-gray-100 text-gray-500' }}">
                        {{ ucfirst($enrollment->status) }}
                    </span>
                </div>

                {{-- Action buttons — only for active enrollments --}}
                @if ($enrollment->status === 'active')
                    <div class="flex gap-2 mt-1">

                        {{-- Transfer — same year, different class --}}
                        <button onclick="openModal('transfer-{{ $enrollment->id }}')"
                                class="text-xs px-2 py-1 bg-blue-50 border border-blue-200
                                       text-blue-700 rounded hover:bg-blue-100 transition-colors">
                            ↔ Transfer
                        </button>

                        {{-- Promote — next year --}}
                        <button onclick="openModal('promote-{{ $enrollment->id }}')"
                                class="text-xs px-2 py-1 bg-purple-50 border border-purple-200
                                       text-purple-700 rounded hover:bg-purple-100 transition-colors">
                            ↑ Promote
                        </button>

                    </div>

                    {{-- Transfer Modal --}}
                    <div id="transfer-{{ $enrollment->id }}"
                         class="hidden fixed inset-0 bg-black bg-opacity-40 z-50
                                flex items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-lg border border-gray-200
                                    w-full max-w-sm p-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-1">
                                Transfer Student
                            </h4>
                            <p class="text-xs text-gray-400 mb-4">
                                Move to a different class in the same academic year.
                            </p>
                            <form method="POST"
                                  action="{{ route('admin.enrollments.transfer', $enrollment) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs text-gray-500 mb-1">
                                        New Class ({{ $enrollment->schoolClass->academicYear->name }})
                                    </label>
                                    <select name="class_id" required
                                            class="w-full border border-gray-300 rounded px-2 py-1.5
                                                   text-sm focus:outline-none focus:ring-2
                                                   focus:ring-blue-400">
                                        <option value="">— Select Class —</option>
                                        @foreach ($sameYearClasses[$enrollment->id] ?? [] as $cls)
                                            <option value="{{ $cls->id }}">
                                                {{ $cls->name }} ({{ $cls->grade->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 justify-end">
                                    <button type="button"
                                            onclick="closeModal('transfer-{{ $enrollment->id }}')"
                                            class="px-3 py-1.5 text-xs text-gray-600 bg-gray-100
                                                   rounded hover:bg-gray-200">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs text-white bg-blue-600
                                                   rounded hover:bg-blue-700">
                                        Confirm Transfer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Promote Modal --}}
                    <div id="promote-{{ $enrollment->id }}"
                         class="hidden fixed inset-0 bg-black bg-opacity-40 z-50
                                flex items-center justify-center p-4">
                        <div class="bg-white rounded-lg shadow-lg border border-gray-200
                                    w-full max-w-sm p-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-1">
                                Promote Student
                            </h4>
                            <p class="text-xs text-gray-400 mb-4">
                                Move to a class in the next academic year.
                            </p>
                            <form method="POST"
                                  action="{{ route('admin.enrollments.promote', $enrollment) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-xs text-gray-500 mb-1">
                                        New Class (Next Year)
                                    </label>
                                    <select name="class_id" required
                                            class="w-full border border-gray-300 rounded px-2 py-1.5
                                                   text-sm focus:outline-none focus:ring-2
                                                   focus:ring-blue-400">
                                        <option value="">— Select Class —</option>
                                        @foreach ($nextYearClasses as $cls)
                                            <option value="{{ $cls->id }}">
                                                {{ $cls->name }} ({{ $cls->grade->name }})
                                                · {{ $cls->academicYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="flex gap-2 justify-end">
                                    <button type="button"
                                            onclick="closeModal('promote-{{ $enrollment->id }}')"
                                            class="px-3 py-1.5 text-xs text-gray-600 bg-gray-100
                                                   rounded hover:bg-gray-200">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs text-white bg-purple-600
                                                   rounded hover:bg-purple-700">
                                        Confirm Promotion
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400">No enrollments yet.</p>
        @endforelse
    </div>

</div>

@push('scripts')
<script>
function openModal(id)  { document.getElementById(id).classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

// Close modal on backdrop click
document.querySelectorAll('[id^="transfer-"], [id^="promote-"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) closeModal(this.id);
    });
});
</script>
@endpush

@endsection