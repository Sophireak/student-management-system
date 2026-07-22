@extends('layouts.admin', ['title' => 'Teachers'])

@section('content')

<div x-data="{
    deleteModal: false,
    teacherName: '',
    teacherId: null,
    openDelete(id, name) {
        this.teacherId = id;
        this.teacherName = name;
        this.deleteModal = true;
    },
    closeDelete() {
        this.deleteModal = false;
        this.teacherId = null;
        this.teacherName = '';
    }
}">

    {{-- ========================================
         TOOLBAR
         ======================================== --}}
    <x-admin.page-toolbar>
        <x-slot:left>
            <x-admin.toolbar-meta 
                icon="ti-school"
                label="Total Teachers"
                value="{{ $teachers->total() }} registered"
                color="green" />
        </x-slot:left>

        <x-slot:right>
            <x-admin.toolbar-button 
                href="{{ route('admin.teachers.create') }}"
                icon="ti-user-plus"
                label="Add Teacher"
                variant="primary" />
        </x-slot:right>
    </x-admin.page-toolbar>

    {{-- ========================================
         SEARCH
         ======================================== --}}
    <x-admin.toolbar-search 
        :action="route('admin.teachers.index')"
        placeholder="Search by name, email or employee ID..."
        :value="$search ?? ''" />

    {{-- ========================================
         TABLE (keep existing table code)
         ======================================== --}}
    <div class="bg-white border border-gray-200 rounded-2xl 
                shadow-sm overflow-hidden mb-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-200">
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap">
                            Teacher
                        </th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap 
                                   hidden md:table-cell">
                            Contact
                        </th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap">
                            Employee ID
                        </th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-400 
                                   uppercase tracking-widest whitespace-nowrap 
                                   text-right">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100/80">
                    @forelse ($teachers as $teacher)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl 
                                                bg-gradient-to-br from-green-100 to-emerald-100 
                                                text-green-700 flex items-center justify-center 
                                                font-bold shadow-inner flex-shrink-0">
                                        {{ strtoupper(substr($teacher->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.teachers.show', $teacher) }}" 
                                           class="text-sm font-bold text-gray-800 
                                                  hover:text-green-600 transition-colors">
                                            {{ $teacher->user->name }}
                                        </a>
                                        <p class="text-[11px] text-gray-400 mt-0.5">
                                            {{ $teacher->gender 
                                                ? ucfirst($teacher->gender) 
                                                : 'Teacher' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4 hidden md:table-cell">
                                <div class="flex flex-col gap-1.5">
                                    <div class="flex items-center gap-2 text-sm text-gray-600">
                                        <i class="ti ti-mail text-gray-400 text-xs"></i>
                                        {{ $teacher->user->email }}
                                    </div>
                                    @if($teacher->phone)
                                        <div class="flex items-center gap-2 text-xs text-gray-400">
                                            <i class="ti ti-phone text-gray-400 text-xs"></i>
                                            {{ $teacher->phone }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                @if($teacher->employee_id)
                                    <span class="inline-flex items-center gap-1.5 
                                                 px-2.5 py-1 rounded-lg text-xs 
                                                 font-mono font-semibold 
                                                 bg-gray-50 text-gray-600 
                                                 border border-gray-200">
                                        <i class="ti ti-id-badge text-gray-400"></i>
                                        {{ $teacher->employee_id }}
                                    </span>
                                @else
                                    <span class="text-xs font-medium text-gray-400 
                                                 bg-gray-50 px-2 py-1 rounded-lg 
                                                 border border-gray-100">
                                        Not set
                                    </span>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.teachers.show', $teacher) }}"
                                       class="w-8 h-8 rounded-lg flex items-center 
                                              justify-center text-gray-400 bg-gray-50
                                              hover:bg-blue-50 hover:text-blue-600 
                                              transition-all border border-gray-100 
                                              hover:border-blue-100"
                                       title="View Details">
                                        <i class="ti ti-eye text-lg"></i>
                                    </a>

                                    <a href="{{ route('admin.teachers.edit', $teacher) }}"
                                       class="w-8 h-8 rounded-lg flex items-center 
                                              justify-center text-gray-400 bg-gray-50
                                              hover:bg-amber-50 hover:text-amber-600 
                                              transition-all border border-gray-100 
                                              hover:border-amber-100"
                                       title="Edit Teacher">
                                        <i class="ti ti-pencil text-lg"></i>
                                    </a>

                                    <button
                                        @click="openDelete({{ $teacher->id }}, '{{ addslashes($teacher->user->name) }}')"
                                        type="button"
                                        class="w-8 h-8 rounded-lg flex items-center 
                                               justify-center text-gray-400 bg-gray-50
                                               hover:bg-red-50 hover:text-red-600 
                                               transition-all border border-gray-100 
                                               hover:border-red-100"
                                        title="Deactivate Teacher">
                                        <i class="ti ti-user-off text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-16 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-gray-50 
                                            flex items-center justify-center 
                                            mx-auto mb-4 border border-gray-100">
                                    <i class="ti ti-school-off text-2xl text-gray-400"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-800 mb-1">
                                    No teachers found
                                </h3>
                                <p class="text-sm text-gray-500 mb-4">
                                    @if($search)
                                        No teachers match 
                                        "<span class="font-semibold">{{ $search }}</span>"
                                    @else
                                        Get started by adding your first teacher.
                                    @endif
                                </p>
                                @if($search)
                                    <a href="{{ route('admin.teachers.index') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 
                                              bg-white border border-gray-200 rounded-xl 
                                              text-sm font-semibold text-gray-600 
                                              hover:bg-gray-50 transition-colors">
                                        <i class="ti ti-x text-sm"></i>
                                        Clear Search
                                    </a>
                                @else
                                    <a href="{{ route('admin.teachers.create') }}"
                                       class="inline-flex items-center gap-2 px-4 py-2 
                                              bg-green-600 text-white rounded-xl 
                                              text-sm font-semibold 
                                              hover:bg-green-700 transition-colors">
                                        <i class="ti ti-user-plus"></i>
                                        Add First Teacher
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($teachers->hasPages())
        <div class="bg-white border border-gray-200 rounded-2xl 
                    px-4 py-3 shadow-sm">
            {{ $teachers->links() }}
        </div>
    @endif

    {{-- ========================================
         DEACTIVATE MODAL
         ======================================== --}}
    <div
        x-show="deleteModal"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center 
               justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="closeDelete()"
        @keydown.escape.window="closeDelete()"
    >
        <div
            x-show="deleteModal"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm"
        >
            <div class="w-12 h-12 rounded-2xl bg-red-50 
                        flex items-center justify-center mx-auto mb-4">
                <i class="ti ti-user-off text-red-500 text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800 text-center mb-1">
                Deactivate Teacher?
            </h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Are you sure you want to deactivate 
                <span class="font-semibold text-gray-700" 
                      x-text="teacherName">
                </span>?
                They will lose access to the system.
            </p>
            <div class="flex gap-3">
                <button
                    @click="closeDelete()"
                    type="button"
                    class="flex-1 px-4 py-2.5 bg-gray-100 
                           hover:bg-gray-200 text-gray-600 
                           text-sm font-semibold rounded-xl 
                           transition-colors">
                    Cancel
                </button>
                <form
                    method="POST"
                    :action="'/admin/teachers/' + teacherId"
                    class="flex-1"
                >
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full px-4 py-2.5 bg-red-500 
                                   hover:bg-red-600 text-white 
                                   text-sm font-semibold rounded-xl 
                                   transition-colors">
                        Yes, Deactivate
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection