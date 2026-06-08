@extends('layouts.admin', ['title' => 'Academic Years'])

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Academic Years</h1>
        <p class="text-sm text-gray-500 mt-1">Manage school academic years</p>
    </div>
    <a href="{{ route('admin.academic-years.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
        <i class="ti ti-calendar-plus text-base"></i> New Year
    </a>
</div>

{{-- Search --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-5">
    <form method="GET" action="" class="flex flex-wrap gap-3 items-end">
        <div class="relative flex-1 min-w-48">
            <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-base"></i>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search academic years..."
                   class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm
                          focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
        </div>
        <button type="submit"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
            <i class="ti ti-search text-base"></i> Search
        </button>
        @if ($search ?? false)
            <a href="{{ route('admin.academic-years.index') }}"
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
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Start Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">End Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($academicYears as $year)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg {{ $year->is_active ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-calendar {{ $year->is_active ? 'text-green-600' : 'text-gray-400' }} text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800">{{ $year->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $year->start_date?->format('M d, Y') ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $year->end_date?->format('M d, Y') ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if ($year->is_active)
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                        @else
                            <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.academic-years.show', $year) }}"
                               class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors" title="View">
                                <i class="ti ti-eye text-sm"></i>
                            </a>
                            <a href="{{ route('admin.academic-years.edit', $year) }}"
                               class="p-1.5 rounded-lg bg-yellow-50 hover:bg-yellow-100 text-yellow-600 transition-colors" title="Edit">
                                <i class="ti ti-pencil text-sm"></i>
                            </a>
                            @if (!$year->is_active)
                                <form method="POST" action="{{ route('admin.academic-years.activate', $year) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg bg-green-50 hover:bg-green-100 text-green-600 transition-colors" title="Activate">
                                        <i class="ti ti-check text-sm"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-12 text-center">
                        <i class="ti ti-calendar-off text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-400 text-sm">No academic years found.</p>
                        <a href="{{ route('admin.academic-years.create') }}"
                           class="mt-3 inline-flex items-center gap-1 text-sm text-green-600 hover:underline">
                            <i class="ti ti-calendar-plus"></i> Add first year
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $academicYears->links() }}</div>

@endsection
