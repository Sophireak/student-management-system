@extends('layouts.admin', ['title' => $academicYear->name])

@section('content')

<div class="mb-6">
    <a href="{{ route('admin.academic-years.index') }}"
       class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700 mb-4">
        <i class="ti ti-arrow-left text-base"></i> Back to Academic Years
    </a>
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <i class="ti ti-calendar text-green-600 text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $academicYear->name }}</h1>
                <div class="mt-1">
                    @if ($academicYear->is_active)
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-green-100 text-green-700">Active</span>
                    @else
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-gray-100 text-gray-500">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if (!$academicYear->is_active)
                <form method="POST" action="{{ route('admin.academic-years.activate', $academicYear) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <i class="ti ti-check text-base"></i> Activate
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.academic-years.edit', $academicYear) }}"
               class="flex items-center gap-2 px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 text-sm font-medium rounded-lg border border-yellow-200 transition-colors">
                <i class="ti ti-pencil text-base"></i> Edit
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Year Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Year Information</h2>

        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-calendar text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Name</p>
                    <p class="text-sm font-medium text-gray-700">{{ $academicYear->name }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-calendar-event text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">Start Date</p>
                    <p class="text-sm font-medium text-gray-700">{{ $academicYear->start_date?->format('M d, Y') ?? '—' }}</p>
                </div>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                    <i class="ti ti-calendar-event text-gray-400 text-base"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400">End Date</p>
                    <p class="text-sm font-medium text-gray-700">{{ $academicYear->end_date?->format('M d, Y') ?? '—' }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Classes in this year --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-5">
        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">Classes This Year</h2>

        @forelse ($academicYear->schoolClasses()->with('grade')->get() as $class)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center">
                        <i class="ti ti-building text-green-600 text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">{{ $class->name }}</p>
                        <p class="text-xs text-gray-400">{{ $class->grade->name }}</p>
                    </div>
                </div>
                <a href="{{ route('admin.classes.show', $class) }}"
                   class="p-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 transition-colors">
                    <i class="ti ti-chevron-right text-sm"></i>
                </a>
            </div>
        @empty
            <div class="py-10 text-center">
                <i class="ti ti-building-off text-4xl text-gray-300 block mb-2"></i>
                <p class="text-sm text-gray-400">No classes for this year yet.</p>
            </div>
        @endforelse
    </div>

</div>

@endsection
