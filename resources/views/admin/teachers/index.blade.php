@extends('layouts.admin', ['title' => 'Teachers'])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-700">Teachers</h2>
    <a href="{{ route('admin.teachers.create') }}"
       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
        + New Teacher
    </a>
</div>

<div class="mb-4">
    <form method="GET" action="" class="flex flex-wrap gap-2 items-end">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Search by name, email or ID..."
               class="border border-gray-300 rounded px-3 py-1.5 text-sm min-w-64
                      focus:outline-none focus:ring-2 focus:ring-blue-400">
        <button type="submit"
                class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
            Search
        </button>
        @if ($search ?? false)
            <a href="{{ route('admin.teachers.index') }}"
               class="px-4 py-1.5 bg-gray-100 text-gray-600 text-sm rounded hover:bg-gray-200">
                Clear
            </a>
        @endif
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Email</th>
                <th class="px-4 py-3 text-left">Employee ID</th>
                <th class="px-4 py-3 text-left">Phone</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($teachers as $teacher)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $teacher->user->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $teacher->user->email }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $teacher->employee_id ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $teacher->phone ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.teachers.show', $teacher) }}"
                               class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">View</a>
                            <a href="{{ route('admin.teachers.edit', $teacher) }}"
                               class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">Edit</a>
                            <form method="POST" action="{{ route('admin.teachers.destroy', $teacher) }}"
                                  onsubmit="return confirm('Deactivate {{ $teacher->user->name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200">
                                    Deactivate
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">No teachers found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $teachers->links() }}</div>

@endsection