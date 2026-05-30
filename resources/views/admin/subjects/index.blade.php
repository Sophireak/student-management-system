@extends('layouts.admin', ['title' => 'Subjects'])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-700">Subjects</h2>
    <a href="{{ route('admin.subjects.create') }}"
       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
        + New Subject
    </a>
</div>

<div class="mb-4">
    <form method="GET" action="" class="flex flex-wrap gap-2 items-end">
        <input type="text" name="search" value="{{ $search ?? '' }}"
               placeholder="Search by name or code..."
               class="border border-gray-300 rounded px-3 py-1.5 text-sm min-w-56
                      focus:outline-none focus:ring-2 focus:ring-blue-400">
        <select name="grade_id"
                class="border border-gray-300 rounded px-2 py-1.5 text-sm
                       focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">All Grades</option>
            @foreach ($grades as $grade)
                <option value="{{ $grade->id }}"
                    {{ ($gradeId ?? '') == $grade->id ? 'selected' : '' }}>
                    {{ $grade->name }}
                </option>
            @endforeach
        </select>
        <button type="submit"
                class="px-4 py-1.5 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
            Search
        </button>
        @if (($search ?? false) || ($gradeId ?? false))
            <a href="{{ route('admin.subjects.index') }}"
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
                <th class="px-4 py-3 text-left">Code</th>
                <th class="px-4 py-3 text-left">Name</th>
                <th class="px-4 py-3 text-left">Grade</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-left">Max Score</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($subjects as $subject)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $subject->code ?? '—' }}</td>
                    <td class="px-4 py-3 font-medium">{{ $subject->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $subject->grade->name }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full
                            {{ $subject->score_type === 'numeric'   ? 'bg-blue-100 text-blue-700'   :
                               ($subject->score_type === 'grade'    ? 'bg-purple-100 text-purple-700' :
                                                                       'bg-gray-100 text-gray-600') }}">
                            {{ ucfirst($subject->score_type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">
                        {{ $subject->score_type === 'numeric' ? $subject->max_score : '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.subjects.edit', $subject) }}"
                               class="text-xs px-2 py-1 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                                  onsubmit="return confirm('Delete {{ $subject->name }}?')">
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
                    <td colspan="6" class="px-4 py-6 text-center text-gray-400">No subjects found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $subjects->links() }}</div>

@endsection
