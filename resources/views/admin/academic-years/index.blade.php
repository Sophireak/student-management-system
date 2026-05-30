@extends('layouts.admin', ['title' => 'Academic Years'])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-700">Academic Years</h2>
    <a href="{{ route('admin.academic-years.create') }}"
       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
        + New Year
    </a>
</div>

<div class="mb-4">
    <form method="GET" action="" class="flex flex-wrap gap-2 items-end">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Search academic years..."
               class="border border-gray-300 rounded px-3 py-1.5 text-sm min-w-64
                      focus:outline-none focus:ring-2 focus:ring-blue-400">
        <button type="submit"
                class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
            Search
        </button>
        @if ($search ?? false)
            <a href="{{ route('admin.academic-years.index') }}"
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
                <th class="px-4 py-3 text-left">Start Date</th>
                <th class="px-4 py-3 text-left">End Date</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($academicYears as $year)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $year->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $year->start_date?->format('M d, Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $year->end_date?->format('M d, Y') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($year->is_active)
                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Active</span>
                        @else
                            <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-500">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.academic-years.edit', $year) }}"
                               class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">Edit</a>
                            @if (!$year->is_active)
                                <form method="POST" action="{{ route('admin.academic-years.activate', $year) }}">
                                    @csrf @method('PATCH')
                                    <button class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-gray-400">No academic years found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $academicYears->links() }}</div>

@endsection