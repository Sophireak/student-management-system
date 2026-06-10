@extends('layouts.admin', ['title' => 'Attendance Sessions'])

@section('content')

{{-- Page Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Attendance Sessions</h1>
        <p class="text-sm text-gray-500 mt-1">Track and manage class attendance</p>
    </div>
    <a href="{{ route('admin.attendance-sessions.create') }}"
       class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
        <i class="ti ti-calendar-plus text-base"></i> New Session
    </a>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="min-w-full divide-y divide-gray-100 text-sm">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Class</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Subject</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Period</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Marked</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($sessions as $session)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-calendar text-green-600 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800">{{ $session->session_date->format('M d, Y') }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-gray-700">{{ $session->schoolClass->name }}</span>
                        <span class="text-gray-400 text-xs ml-1">· {{ $session->schoolClass->grade->name }}</span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $session->subject->name }}</td>
                    <td class="px-4 py-3 capitalize text-gray-500">{{ $session->period ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full
                            {{ $session->attendances_count > 0 ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $session->attendances_count }} students
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.attendance-sessions.show', $session) }}"
                               class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors" title="View">
                                <i class="ti ti-eye text-sm"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.attendance-sessions.destroy', $session) }}"
                                  onsubmit="return confirm('Delete this session and all attendance records?')">
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
                    <td colspan="6" class="px-4 py-12 text-center">
                        <i class="ti ti-calendar-off text-4xl text-gray-300 block mb-2"></i>
                        <p class="text-gray-400 text-sm">No sessions yet.</p>
                        <a href="{{ route('admin.attendance-sessions.create') }}"
                           class="mt-3 inline-flex items-center gap-1 text-sm text-green-600 hover:underline">
                            <i class="ti ti-calendar-plus"></i> Create first session
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
