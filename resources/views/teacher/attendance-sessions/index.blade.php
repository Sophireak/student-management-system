@extends('layouts.teacher', ['title' => 'Attendance Sessions'])

@push('navbar-actions')
    <a href="{{ route('teacher.attendance-sessions.create') }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
        <i class="ti ti-plus text-base"></i>
        <span class="hidden sm:inline">New Session</span>
    </a>
@endpush

@section('content')

<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs tracking-wider">
            <tr>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Class</th>
                <th class="px-4 py-3 text-left">Subject</th>
                <th class="px-4 py-3 text-left">Period</th>
                <th class="px-4 py-3 text-left">Status</th>
                <th class="px-4 py-3 text-left">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 text-gray-700">
            @forelse ($sessions as $session)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">
                        {{ $session->session_date->format('M d, Y') }}
                    </td>
                    <td class="px-4 py-3">{{ $session->schoolClass->name }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $session->subject->name }}</td>
                    <td class="px-4 py-3 capitalize text-gray-500">
                        {{ $session->period ?? '—' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $session->attendances_count > 0
                                ? 'bg-green-100 text-green-700'
                                : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $session->attendances_count > 0 ? 'Marked' : 'Pending' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <a href="{{ route('teacher.attendance-sessions.show', $session) }}"
                           class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200">
                            Open
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                        No sessions yet.
                        <a href="{{ route('teacher.attendance-sessions.create') }}"
                           class="text-green-600 hover:underline ml-1">Create one now.</a>
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
