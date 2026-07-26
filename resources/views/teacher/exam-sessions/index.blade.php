@extends('layouts.teacher', ['title' => 'My Exam Sessions'])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-700">Exam Sessions</h2>
    <a href="{{ route('teacher.exam-sessions.create') }}"
       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
        + New Exam Session
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Exam</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Subject</th>
                <th class="px-4 py-3 text-left">Max</th>
                <th class="px-4 py-3 text-left">Scored</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($examSessions as $session)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $session->full_label }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full capitalize
                            {{ match($session->type) {
                                'quiz'     => 'bg-purple-100 text-purple-700',
                                'monthly'  => 'bg-blue-100 text-blue-700',
                                'semester' => 'bg-yellow-100 text-yellow-700',
                                'final'    => 'bg-red-100 text-red-700',
                            } }}">
                            {{ ucfirst($session->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $session->schoolClass->name }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $session->subject->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $session->max_score }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $session->scores_count > 0
                                ? 'bg-green-100 text-green-700'
                                : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $session->scores_count > 0 ? 'Scored' : 'Pending' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('teacher.exam-sessions.show', $session) }}"
                           class="text-xs px-2 py-1 bg-blue-100 text-blue-700
                                  rounded hover:bg-blue-200">
                            Open
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-gray-400">
                        No exam sessions yet.
                        <a href="{{ route('teacher.exam-sessions.create') }}"
                           class="text-blue-500 hover:underline ml-1">
                            Create one now.
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($examSessions->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $examSessions->links() }}
        </div>
    @endif
</div>

@endsection