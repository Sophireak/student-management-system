@extends('layouts.admin', ['title' => 'Grades'])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-700">Grades</h2>
    <a href="{{ route('admin.grades.create') }}"
       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
        + New Grade
    </a>
</div>

<div class="mb-4">
    <form method="GET" action="" class="flex flex-wrap gap-2 items-end">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Search grades..."
               class="border border-gray-300 rounded px-3 py-1.5 text-sm min-w-64
                      focus:outline-none focus:ring-2 focus:ring-blue-400">
        <button type="submit"
                class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
            Search
        </button>
        @if ($search ?? false)
            <a href="{{ route('admin.grades.index') }}"
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
                <th class="px-4 py-3 text-left">Level</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($grades as $grade)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-500">{{ $grade->level }}</td>
                    <td class="px-4 py-3 font-medium">{{ $grade->name }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.grades.edit', $grade) }}"
                               class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">Edit</a>
                            <form method="POST" action="{{ route('admin.grades.destroy', $grade) }}"
                                  onsubmit="return confirm('Delete {{ $grade->name }}?')">
                                @csrf @method('DELETE')
                                <button class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-gray-400">No grades found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $grades->links() }}</div>

@endsection