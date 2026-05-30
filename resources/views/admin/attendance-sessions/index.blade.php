@extends('layouts.admin', ['title' => 'Attendance Sessions'])

@section('content')

<div class="mb-4 flex items-center justify-between">
    <h2 class="text-lg font-semibold text-gray-700">Attendance Sessions</h2>
    <a href="{{ route('admin.attendance-sessions.create') }}"
       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700">
        + New Session
    </a>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Subject</th>
                <th class="px-4 py-3 text-left">Period</th>
                <th class="px-4 py-3 text-left">Marked</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($sessions as $session)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">
                        {{ $session->session_date->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3">
                        {{ $session->schoolClass->name }}
                        <span class="text-gray-400 text-xs">
                            · {{ $session->schoolClass->grade->name }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">
                        {{ $session->subject->name }}
                    </td>
                    <td class="px-4 py-3 capitalize text-gray-500">
                        {{ $session->period ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $session->attendances_count > 0
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-100 text-gray-500' }}">
                            {{ $session->attendances_count }} students
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">

                            <a href="{{ route('admin.attendance-sessions.show', $session) }}"
                               class="text-xs px-2 py-1 bg-blue-100 text-blue-700
                                      rounded hover:bg-blue-200">
                                View
                            </a>

                            <form method="POST"
                                  action="{{ route('admin.attendance-sessions.destroy', $session) }}"
                                  onsubmit="return confirm('Delete this session and all attendance records?')">
                                @csrf
                                @method('DELETE')
                                <button class="text-xs px-2 py-1 bg-red-100 text-red-700
                                               rounded hover:bg-red-200">
                                    Delete
                                </button>
                            </form>

                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-400">
                        No sessions yet.
                        <a href="{{ route('admin.attendance-sessions.create') }}"
                           class="text-blue-500 hover:underline ml-1">
                            Create one now.
                        </a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if ($sessions->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $sessions->links() }}
        </div>
    @endif
</div>

@endsection