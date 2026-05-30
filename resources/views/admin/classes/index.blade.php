@extends('layouts.admin', ['title' => 'Classes'])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-700">Classes</h2>
    <a href="{{ route('admin.classes.create') }}"
       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
        + New Class
    </a>
</div>

<div class="mb-4">
    <form method="GET" action="" class="flex flex-wrap gap-2 items-end">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Search by class or grade name..."
               class="border border-gray-300 rounded px-3 py-1.5 text-sm min-w-64
                      focus:outline-none focus:ring-2 focus:ring-blue-400">
        <select name="academic_year_id"
                class="border border-gray-300 rounded px-2 py-1.5 text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Years</option>
            @foreach ($academicYears as $year)
                <option value="{{ $year->id }}"
                    {{ ($yearId ?? '') == $year->id ? 'selected' : '' }}>
                    {{ $year->name }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
            Search
        </button>
        @if (($search ?? false) || ($yearId ?? false))
            <a href="{{ route('admin.classes.index') }}"
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
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Grade</th>
                <th class="px-4 py-3 text-left">Academic Year</th>
                <th class="px-4 py-3 text-left">Capacity</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($classes as $class)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $class->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $class->grade->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $class->academicYear->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $class->capacity }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.classes.show', $class) }}"
                               class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200">View</a>
                            <a href="{{ route('admin.classes.edit', $class) }}"
                               class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">Edit</a>
                            <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                  onsubmit="return confirm('Delete {{ $class->name }}?')">
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
                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">No classes found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $classes->links() }}</div>

@endsection