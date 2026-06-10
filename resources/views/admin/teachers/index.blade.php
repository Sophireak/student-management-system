@extends('layouts.admin', ['title' => 'Teachers'])

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Teachers</h1>
        <p class="text-sm text-gray-500 mt-1">Manage all teaching staff</p>
    </div>
    <a href="{{ route('admin.teachers.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
        <i class="ti ti-user-plus text-base"></i> New Teacher
    </a>
</div>

{{-- Search --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" action="" class="flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-56">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by name, email or ID..."
                   class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="ti ti-search text-base"></i> Search
        </button>
        @if ($search ?? false)
            <a href="{{ route('admin.teachers.index') }}"
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
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Employee ID</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($teachers as $teacher)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-user text-green-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800">{{ $teacher->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $teacher->user->email }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-gray-400">{{ $teacher->employee_id ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $teacher->phone ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.teachers.show', $teacher) }}"
                               class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors" title="View">
                                <i class="ti ti-eye text-sm"></i>
                            </a>
                            <a href="{{ route('admin.teachers.edit', $teacher) }}"
                               class="p-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 transition-colors" title="Edit">
                                <i class="ti ti-pencil text-sm"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                                  onsubmit="return confirm('Deactivate {{ $teacher->user->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors" title="Deactivate">
                                    <i class="ti ti-user-off text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center">
                        <i class="ti ti-user-off text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-400 text-sm">No teachers found.</p>
                        <a href="{{ route('admin.teachers.create') }}"
                           class="mt-3 inline-flex items-center gap-1 text-sm text-green-600 hover:underline">
                            <i class="ti ti-user-plus"></i> Add first teacher
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $teachers->links() }}</div>

@endsection
