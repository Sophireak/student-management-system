@extends('layouts.admin', ['title' => 'Subjects'])

@section('content')

{{-- ========================================
     TOOLBAR
     ======================================== --}}
<x-admin.page-toolbar>
    <x-slot:left>
        <x-admin.toolbar-meta 
            icon="ti-book"
            label="Total Subjects"
            value="{{ $subjects->total() }} subjects"
            color="blue" />
    </x-slot:left>

    <x-slot:right>
        {{-- Template Button --}}
        <button onclick="document.getElementById('template-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 
                       text-gray-700 text-sm font-semibold rounded-xl transition-all 
                       hover:bg-amber-50 hover:border-amber-200 hover:text-amber-700
                       active:scale-[0.98]">
            <i class="ti ti-template text-base"></i>
            <span class="hidden sm:inline">Auto-Fill Template</span>
            <span class="sm:hidden">Template</span>
        </button>

        <x-admin.toolbar-button 
            href="{{ route('admin.subjects.create') }}"
            icon="ti-book-plus"
            label="Add Subject"
            variant="primary" />
    </x-slot:right>
</x-admin.page-toolbar>

{{-- ========================================
     TEMPLATE MODAL
     ======================================== --}}
<div id="template-modal" 
     class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 
            flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 
                w-full max-w-md overflow-hidden">
        
        {{-- Modal Header --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="ti ti-template text-amber-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-800">Cambodia Primary School Template</h3>
                <p class="text-xs text-gray-400">Auto-fill standard subjects for a grade</p>
            </div>
        </div>

        {{-- Subject Preview --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">
                16 subjects will be created:
            </p>
            <div class="flex flex-wrap gap-1.5">
                @php
                    $templateSubjects = [
                        'សរសេរតាមអាន', 'អំណានយល់ន័យ', 'តែងសេចក្តី', 'គណិតវិទ្យា',
                        'វិទ្យាសាស្ត្រអនុវត្ត', 'សិក្សាសង្គម', 'អប់រំកាយ សុខភាព កីឡា',
                        'ស្តាប់', 'និយាយ', 'គំនូរ', 'អក្សរផ្ចង់', 'ភាសាបរទេស',
                        'វិន័យ-សីលធម៍រស់នៅ', 'កិច្ចការផ្ទះ', 'កីឡា-ពលកម្ម',
                        'អប់រំបំណិនជីវិតតាមមូលដ្ឋាន',
                    ];
                @endphp
                @foreach($templateSubjects as $subj)
                    <span class="px-2 py-0.5 text-[10px] font-medium rounded-full 
                                 bg-white border border-gray-200 text-gray-600">
                        {{ $subj }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('admin.subjects.template') }}">
            @csrf
            <div class="p-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Select Grade <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="ti ti-award text-gray-400"></i>
                    </div>
                    <select name="grade_id" required
                            class="w-full rounded-xl pl-10 pr-4 py-2.5 text-sm transition-all 
                                   appearance-none cursor-pointer
                                   border-gray-200 bg-gray-50 focus:bg-white 
                                   focus:border-amber-500 focus:ring-2 focus:ring-amber-100">
                        <option value="">Select a grade</option>
                        @foreach ($grades as $grade)
                            <option value="{{ $grade->id }}">{{ $grade->name }}</option>
                        @endforeach
                    </select>
                </div>
                <p class="mt-1.5 text-xs text-gray-400">
                    Subjects that already exist for this grade will be skipped.
                </p>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex gap-2 justify-end">
                <button type="button"
                        onclick="document.getElementById('template-modal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-semibold text-gray-600 
                               bg-white border border-gray-200 rounded-xl 
                               hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold 
                               text-white bg-amber-600 rounded-xl hover:bg-amber-700 
                               transition-colors active:scale-[0.98]">
                    <i class="ti ti-template text-lg"></i>
                    Apply Template
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ========================================
     SEARCH + GRADE FILTER
     ======================================== --}}
<div class="bg-white p-2 rounded-2xl border border-gray-200 mb-5 shadow-sm">
    <form method="GET" action="{{ route('admin.subjects.index') }}" 
          class="flex flex-col sm:flex-row gap-2">

        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="ti ti-search text-gray-400"></i>
            </div>
            <input type="text" name="search" value="{{ $search ?? '' }}"
                   placeholder="Search by name or code..."
                   class="w-full bg-gray-50 border-transparent focus:bg-white focus:border-green-500 
                          focus:ring-2 focus:ring-green-200 rounded-xl pl-10 pr-4 py-2.5 text-sm 
                          text-gray-700 transition-all placeholder-gray-400">
        </div>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="ti ti-award text-gray-400"></i>
            </div>
            <select name="grade_id"
                    class="bg-gray-50 border-transparent focus:bg-white focus:border-green-500 
                           focus:ring-2 focus:ring-green-200 rounded-xl pl-10 pr-8 py-2.5 text-sm 
                           text-gray-700 transition-all appearance-none cursor-pointer min-w-[150px]">
                <option value="">All Grades</option>
                @foreach ($grades as $grade)
                    <option value="{{ $grade->id }}" 
                            {{ ($gradeId ?? '') == $grade->id ? 'selected' : '' }}>
                        {{ $grade->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit"
                class="px-4 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 text-sm font-semibold 
                       rounded-xl border border-gray-200 transition-colors">
            Filter
        </button>

        @if (($search ?? false) || ($gradeId ?? false))
            <a href="{{ route('admin.subjects.index') }}" title="Clear filters"
               class="px-3 py-2.5 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold 
                      rounded-xl border border-red-100 transition-colors flex items-center">
                <i class="ti ti-x text-lg"></i>
            </a>
        @endif
    </form>
</div>

{{-- ========================================
     TABLE
     ======================================== --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-4">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-200">
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Subject
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden sm:table-cell">
                        Code
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap">
                        Grade
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden md:table-cell">
                        Score Type
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap hidden lg:table-cell">
                        Max Score
                    </th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest whitespace-nowrap text-right">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100/80">
                @forelse ($subjects as $subject)
                    <tr class="hover:bg-gray-50/50 transition-colors group">

                        {{-- Subject Name --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-cyan-100 
                                            text-blue-700 flex items-center justify-center font-bold 
                                            shadow-inner flex-shrink-0 text-sm">
                                    <i class="ti ti-book text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800">
                                        {{ $subject->name }}
                                    </p>
                                    <p class="text-[11px] text-gray-400 mt-0.5 sm:hidden">
                                        {{ $subject->code ?? 'No code' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Code --}}
                        <td class="px-5 py-4 hidden sm:table-cell">
                            @if($subject->code)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-mono font-semibold bg-gray-50 text-gray-600 border border-gray-200">
                                    <i class="ti ti-hash text-gray-400"></i>
                                    {{ $subject->code }}
                                </span>
                            @else
                                <span class="text-xs font-medium text-gray-400 bg-gray-50 px-2 py-1 rounded-lg border border-gray-100">
                                    No code
                                </span>
                            @endif
                        </td>

                        {{-- Grade --}}
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                         font-semibold bg-indigo-50 text-indigo-600 border border-indigo-100">
                                <i class="ti ti-award text-indigo-400"></i>
                                {{ $subject->grade->name }}
                            </span>
                        </td>

                        {{-- Score Type --}}
                        <td class="px-5 py-4 hidden md:table-cell">
                            @if($subject->score_type === 'numeric')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-semibold bg-blue-50 text-blue-600 border border-blue-100">
                                    <i class="ti ti-calculator text-blue-400"></i>
                                    Numeric
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-semibold bg-purple-50 text-purple-600 border border-purple-100">
                                    <i class="ti ti-letter-a text-purple-400"></i>
                                    Grade
                                </span>
                            @endif
                        </td>

                        {{-- Max Score --}}
                        <td class="px-5 py-4 hidden lg:table-cell">
                            @if($subject->score_type === 'numeric')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs 
                                             font-semibold bg-green-50 text-green-600 border border-green-100">
                                    <i class="ti ti-target text-green-400"></i>
                                    {{ $subject->max_score }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.subjects.edit', $subject) }}"
                                   class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                          hover:bg-amber-50 hover:text-amber-600 transition-all border border-gray-100 hover:border-amber-100"
                                   title="Edit Subject">
                                    <i class="ti ti-pencil text-lg"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.subjects.destroy', $subject) }}"
                                      onsubmit="return confirm('Are you sure you want to delete {{ $subject->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 bg-gray-50
                                                   hover:bg-red-50 hover:text-red-600 transition-all border border-gray-100 hover:border-red-100"
                                            title="Delete Subject">
                                        <i class="ti ti-trash text-lg"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <i class="ti ti-book-off text-2xl text-gray-400"></i>
                            </div>
                            <h3 class="text-sm font-bold text-gray-800 mb-1">No subjects found</h3>
                            <p class="text-sm text-gray-500 mb-4">
                                @if($search || $gradeId)
                                    No subjects match your current filters.
                                @else
                                    Get started by adding subjects or use the template.
                                @endif
                            </p>
                            <div class="flex items-center justify-center gap-2">
                                @if($search || $gradeId)
                                    <a href="{{ route('admin.subjects.index') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300
                                              rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                        Clear Filters
                                    </a>
                                @else
                                    <button onclick="document.getElementById('template-modal').classList.remove('hidden')"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-700
                                                   rounded-lg text-sm font-bold hover:bg-amber-100 transition-colors">
                                        <i class="ti ti-template"></i> Use Template
                                    </button>
                                    <a href="{{ route('admin.subjects.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 bg-green-50 text-green-700
                                              rounded-lg text-sm font-bold hover:bg-green-100 transition-colors">
                                        <i class="ti ti-book-plus"></i> Add Manually
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($subjects->hasPages())
    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
        {{ $subjects->links() }}
    </div>
@endif

{{-- Modal close on backdrop click --}}
@push('scripts')
<script>
    document.getElementById('template-modal')?.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });
</script>
@endpush

@endsection