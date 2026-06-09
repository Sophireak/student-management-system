@extends('layouts.admin', ['title' => 'Classes'])

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Classes</h1>
        <p class="text-sm text-gray-500 mt-1">Manage all school classes</p>
    </div>
    <a href="{{ route('admin.classes.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
        <i class="ti ti-building-plus text-base"></i> New Class
    </a>
</div>

{{-- Search & Filter --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" action="" class="flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-48">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by class or grade..."
                   class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <div class="relative">
            <i class="ti ti-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <select name="academic_year_id"
                    class="border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                           focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                <option value="">All Years</option>
                @foreach ($academicYears as $year)
                    <option value="{{ $year->id }}" {{ ($yearId ?? '') == $year->id ? 'selected' : '' }}>
                        {{ $year->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="ti ti-search text-base"></i> Search
        </button>
        @if (($search ?? false) || ($yearId ?? false))
            <a href="{{ route('admin.classes.index') }}"
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
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Class</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Grade</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Academic Year</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Capacity</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($classes as $class)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-building text-green-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800">{{ $class->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $class->grade->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $class->academicYear->name }}</td>
                    <td class="px-4 py-3">
                        <span class="text-gray-500">{{ $class->capacity }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.classes.show', $class) }}"
                               class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors" title="View">
                                <i class="ti ti-eye text-sm"></i>
                            </a>
                            <a href="{{ route('admin.classes.edit', $class) }}"
                               class="p-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 transition-colors" title="Edit">
                                <i class="ti ti-pencil text-sm"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.classes.destroy', $class) }}"
                                  onsubmit="return confirm('Delete {{ $class->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition-colors" title="Delete">
                                    <i class="ti ti-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center">
                        <i class="ti ti-building-off text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-400 text-sm">No classes found.</p>
                        <a href="{{ route('admin.classes.create') }}"
                           class="mt-3 inline-flex items-center gap-1 text-sm text-green-600 hover:underline">
                            <i class="ti ti-building-plus"></i> Add first class
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $classes->links() }}</div>

@endsection
