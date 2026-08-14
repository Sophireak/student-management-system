@extends('layouts.admin', ['title' => 'Students'])

@section('content')

{{-- Root Alpine component for archive modal --}}
<div x-data="{ 
    archiveModal: false, 
    studentName: '', 
    studentId: null,
    openArchive(id, name) {
        this.studentId = id;
        this.studentName = name;
        this.archiveModal = true;
    },
    closeArchive() {
        this.archiveModal = false;
        this.studentId = null;
        this.studentName = '';
    }
}">

    {{-- ========================================
         TOOLBAR
         ======================================== --}}
    <x-admin.page-toolbar>
        <x-slot:left>
            <x-admin.toolbar-tabs 
                filter-key="gender"
                :tabs="[
                    ['key' => 'all',    'label' => 'All',    'count' => $totalStudents ?? $students->total(),  'icon' => 'ti-users',         'color' => 'green'],
                    ['key' => 'male',   'label' => 'Male',   'count' => $maleCount ?? 0,                       'icon' => 'ti-gender-male',   'color' => 'blue'],
                    ['key' => 'female', 'label' => 'Female', 'count' => $femaleCount ?? 0,                     'icon' => 'ti-gender-female', 'color' => 'pink'],
                ]" />
        </x-slot:left>

        <x-slot:right>
            @if (($archivedCount ?? 0) > 0)
                <x-admin.toolbar-button 
                    href="{{ route('admin.students.archived') }}"
                    icon="ti-archive"
                    label="Archived"
                    variant="secondary"
                    :badge="$archivedCount" />
            @endif

            <x-admin.toolbar-button 
                href="{{ route('admin.students.create') }}"
                icon="ti-user-plus"
                label="Add Student"
                variant="primary" />
        </x-slot:right>
    </x-admin.page-toolbar>

    {{-- ========================================
         SEARCH
         ======================================== --}}
    <x-admin.toolbar-search 
        :action="route('admin.students.index')"
        placeholder="Search by name or student ID..."
        :value="$search ?? ''"
        :preserve="['gender' => request('gender')]" />

    {{-- ========================================
         TABLE
         ======================================== --}}
    <div class="bg-white border border-gray-200 rounded-2xl 
                shadow-sm overflow-hidden mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-200">
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap">
                            Student
                        </th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap">
                            Student ID
                        </th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap 
                                   hidden md:table-cell">
                            Gender
                        </th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap 
                                   hidden lg:table-cell">
                            Guardian
                        </th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap 
                                   text-right">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/80">
                    @forelse ($students as $student)
                        <tr class="hover:bg-gray-50/50 transition-colors group">

                            {{-- Student Profile --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden flex items-center 
            justify-center font-bold shadow-inner flex-shrink-0
            {{ $student->gender === 'female' 
                ? 'bg-gradient-to-br from-pink-100 to-rose-100 text-pink-700' 
                : 'bg-gradient-to-br from-blue-100 to-indigo-100 text-blue-700' }}">
    @if ($student->photo)
        <img src="{{ asset('storage/' . $student->photo) }}" 
             alt="{{ $student->full_name }}"
             class="w-full h-full object-cover">
    @else
        {{ strtoupper(substr($student->first_name, 0, 1)) }}
    @endif
</div>
                                    <div>
                                        <a href="{{ route('admin.students.show', $student) }}" 
                                           class="text-sm font-bold text-gray-800 
                                                  hover:text-green-600 transition-colors">
                                            {{ $student->full_name }}
                                        </a>
                                        <p class="text-[11px] text-gray-400 mt-0.5 md:hidden">
                                            {{ ucfirst($student->gender ?? 'N/A') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            {{-- Student ID --}}
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 
                                             px-2.5 py-1 rounded-lg text-xs 
                                             font-mono font-semibold 
                                             bg-gray-50 text-gray-600 
                                             border border-gray-200">
                                    <i class="ti ti-id-badge text-gray-400"></i>
                                    {{ $student->student_id }}
                                </span>
                            </td>

                            {{-- Gender --}}
                            <td class="px-5 py-4 hidden md:table-cell">
                                @if($student->gender)
                                    <span class="inline-flex items-center gap-1.5 
                                                 px-2.5 py-1 rounded-lg text-xs 
                                                 font-semibold
                                                 {{ $student->gender === 'female' 
                                                     ? 'bg-pink-50 text-pink-600 border border-pink-100' 
                                                     : 'bg-blue-50 text-blue-600 border border-blue-100' }}">
                                        <i class="ti {{ $student->gender === 'female' 
                                                        ? 'ti-gender-female' 
                                                        : 'ti-gender-male' }} text-xs">
                                        </i>
                                        {{ ucfirst($student->gender) }}
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-gray-400 
                                                 bg-gray-50 px-2 py-1 rounded-lg 
                                                 border border-gray-100">
                                        Not set
                                    </span>
                                @endif
                            </td>

                            {{-- Guardian --}}
                            <td class="px-5 py-4 hidden lg:table-cell">
                                @if($student->guardian_name)
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1.5 
                                                    text-sm text-gray-600">
                                            <i class="ti ti-user-heart text-gray-400 text-xs"></i>
                                            {{ $student->guardian_name }}
                                        </div>
                                        @if($student->guardian_phone)
                                            <div class="flex items-center gap-1.5 
                                                        text-xs text-gray-400">
                                                <i class="ti ti-phone text-gray-400 text-xs"></i>
                                                {{ $student->guardian_phone }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs font-medium text-gray-400 
                                                 bg-gray-50 px-2 py-1 rounded-lg 
                                                 border border-gray-100">
                                        No guardian
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.students.show', $student) }}"
                                       class="w-8 h-8 rounded-lg flex items-center 
                                              justify-center text-gray-400 bg-gray-50
                                              hover:bg-blue-50 hover:text-blue-600 
                                              transition-all border border-gray-100 
                                              hover:border-blue-100" 
                                       title="View Details">
                                        <i class="ti ti-eye text-lg"></i>
                                    </a>

                                    <a href="{{ route('admin.students.edit', $student) }}"
                                       class="w-8 h-8 rounded-lg flex items-center 
                                              justify-center text-gray-400 bg-gray-50
                                              hover:bg-amber-50 hover:text-amber-600 
                                              transition-all border border-gray-100 
                                              hover:border-amber-100" 
                                       title="Edit Student">
                                        <i class="ti ti-pencil text-lg"></i>
                                    </a>

                                    <button 
                                        @click="openArchive({{ $student->id }}, '{{ addslashes($student->full_name) }}')"
                                        type="button"
                                        class="w-8 h-8 rounded-lg flex items-center 
                                               justify-center text-gray-400 bg-gray-50
                                               hover:bg-red-50 hover:text-red-600 
                                               transition-all border border-gray-100 
                                               hover:border-red-100" 
                                        title="Archive Student">
                                        <i class="ti ti-archive text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-50 
                                            flex items-center justify-center 
                                            mx-auto mb-4 border border-gray-100">
                                    <i class="ti ti-users-off text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-800 mb-1">
                                    No students found
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    @if($search)
                                        No students match 
                                        "<span class="font-semibold">{{ $search }}</span>"
                                    @else
                                        Get started by adding your first student.
                                    @endif
                                </p>
                                @if($search)
                                    <a href="{{ route('admin.students.index') }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 
                                              bg-white border border-gray-200 rounded-xl 
                                              text-sm font-semibold text-gray-600 
                                              hover:bg-gray-50 transition-colors">
                                        <i class="ti ti-x text-sm"></i>
                                        Clear Search
                                    </a>
                                @else
                                    <a href="{{ route('admin.students.create') }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2 
                                              bg-green-600 text-white rounded-xl 
                                              text-sm font-semibold 
                                              hover:bg-green-700 transition-colors">
                                        <i class="ti ti-user-plus"></i>
                                        Add First Student
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($students->hasPages())
        <div class="bg-white border border-gray-200 rounded-2xl 
                    px-4 py-3 shadow-sm">
            {{ $students->links() }}
        </div>
    @endif

    {{-- ========================================
         ARCHIVE MODAL
         ======================================== --}}
    <div 
        x-show="archiveModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center 
               justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="closeArchive()"
        @keydown.escape.window="closeArchive()"
    >
        <div 
            x-show="archiveModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm"
        >
            <div class="w-12 h-12 rounded-2xl bg-red-50 
                        flex items-center justify-center 
                        mx-auto mb-4">
                <i class="ti ti-archive text-red-500 text-xl"></i>
            </div>

            <h3 class="text-base font-bold text-gray-800 text-center mb-1">
                Archive Student?
            </h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Are you sure you want to archive 
                <span class="font-semibold text-gray-700" x-text="studentName"></span>?
                You can restore them later.
            </p>

            <div class="flex gap-3">
                <button 
                    @click="closeArchive()"
                    type="button"
                    class="flex-1 px-4 py-2.5 bg-gray-100 
                           hover:bg-gray-200 text-gray-600 
                           text-sm font-semibold rounded-xl 
                           transition-colors">
                    Cancel
                </button>
                <form 
                    method="POST" 
                    :action="'/admin/students/' + studentId"
                    class="flex-1"
                >
                    @csrf 
                    @method('DELETE')
                    <button 
                        type="submit"
                        class="w-full px-4 py-2.5 bg-red-500 
                               hover:bg-red-600 text-white 
                               text-sm font-semibold rounded-xl 
                               transition-colors">
                        Yes, Archive
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection