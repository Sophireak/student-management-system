@extends('layouts.admin', ['title' => 'Archived Students'])

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Archived Students</h1>
        <p class="text-sm text-gray-500 mt-1">Restore or permanently delete archived students</p>
    </div>
    <a href="{{ route('admin.students.index') }}"
       class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-semibold rounded-lg transition-colors">
        <i class="ti ti-arrow-left text-base"></i> Back to Students
    </a>
</div>

{{-- Flash messages --}}
@if (session('success'))
    <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
        ✅ {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
        ⚠️ {{ session('error') }}
    </div>
@endif

{{-- Search --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" action="" class="flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-56">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by name or ID..."
                   class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="ti ti-search text-base"></i> Search
        </button>
        @if ($search ?? false)
            <a href="{{ route('admin.students.archived') }}"
               class="flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 text-sm font-medium rounded-lg transition-colors">
                <i class="ti ti-x text-base"></i> Clear
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Student ID</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Gender</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Guardian</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Archived At</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($students as $student)
                <tr class="hover:bg-gray-50 transition-colors opacity-75">
                    <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $student->student_id }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-user text-gray-400 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-500">{{ $student->full_name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 capitalize text-gray-400">{{ $student->gender ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ $student->guardian_name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs">
                        {{ $student->deleted_at->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            {{-- Restore --}}
                            <form method="POST"
                                  action="{{ route('admin.students.restore', $student->id) }}"
                                  onsubmit="return confirm('Restore {{ $student->full_name }}?')">
                                @csrf
                                <button type="submit"
                                        class="p-1.5 rounded-lg bg-green-50 hover:bg-green-100 text-green-600 transition-colors"
                                        title="Restore">
                                    <i class="ti ti-restore text-sm"></i>
                                </button>
                            </form>

                            {{-- Permanent Delete --}}
                            <form method="POST"
                                  action="{{ route('admin.students.force-delete', $student->id) }}"
                                  onsubmit="return confirm('Permanently delete {{ $student->full_name }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors"
                                        title="Delete Forever">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center">
                        <i class="ti ti-archive text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-400 text-sm">No archived students.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination --}}
<div class="mt-4">{{ $students->links() }}</div>

@endsection