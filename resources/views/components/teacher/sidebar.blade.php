<aside
    id="sidebar"
    class="w-64 bg-white text-gray-700 flex flex-col flex-shrink-0
           fixed inset-y-0 left-0 z-40 transform -translate-x-full
           transition-all duration-200 ease-in-out
           md:relative md:translate-x-0
           m-0 md:m-3 md:rounded-2xl md:shadow-[0_4px_20px_rgba(0,0,0,0.06)]
           border border-gray-100 md:h-[calc(100%-1.5rem)]"
>
    {{-- Logo + desktop collapse button --}}
<div class="h-16 flex items-center border-b border-gray-100 px-4
            justify-between sidebar-expanded-only
            justify-center sidebar-collapsed-only">

    {{-- Logo (hidden when collapsed) --}}
    <div class="flex items-center gap-2 sidebar-label">
    <span class="text-2xl leading-none">🎓</span>
    <div class="flex flex-col leading-tight">
        <span class="text-sm font-extrabold text-gray-800 tracking-tight">{{ config('app.name') }}</span>
        <span class="text-[10px] font-medium text-green-600 uppercase tracking-widest">School Portal</span>
    </div>
</div>
    {{-- Toggle button (always visible) --}}
    <button
        id="sidebar-toggle-btn"
        class="hidden md:flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors"
        onclick="toggleSidebar()"
        aria-label="Collapse sidebar"
    >
        <i id="sidebar-toggle-icon" class="ti ti-layout-sidebar-left-collapse text-lg"></i>
    </button>
</div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
        <x-admin.nav-item route="teacher.dashboard" icon="ti ti-layout-dashboard" label="Dashboard" />

        <div class="pt-4 pb-1 px-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider sidebar-label">Daily Tasks</div>
        <x-admin.nav-item route="teacher.student-attendance.index" icon="ti ti-calendar-check" label="Take Attendance" />
        <x-admin.nav-item route="teacher.examination-scores.index" icon="ti ti-pencil"          label="Enter Scores" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider sidebar-label">Management</div>
        <x-admin.nav-item route="teacher.students.index" icon="ti ti-users" label="My Students" />

        <div class="pt-4 pb-1 px-3 text-[11px] font-semibold text-gray-400 uppercase tracking-wider sidebar-label">Reports</div>
        <x-admin.nav-group
            icon="ti ti-chart-bar"
            label="Reports"
            :routes="['teacher.monthly-report', 'teacher.semester-report', 'teacher.annual-report', 'teacher.reports.ranking', 'teacher.reports.honors']"
        >
            <x-admin.nav-item route="teacher.monthly-report.index"  icon="ti ti-calendar-stats" label="Monthly Report" />
            <x-admin.nav-item route="teacher.semester-report.index" icon="ti ti-calendar-due"   label="Semester Report" />
            <x-admin.nav-item route="teacher.annual-report.index"   icon="ti ti-calendar-event" label="Annual Report" />
            <x-admin.nav-item route="teacher.reports.ranking.index" icon="ti ti-medal"          label="Ranking" />
            <x-admin.nav-item route="teacher.reports.honors.index"  icon="ti ti-trophy"         label="Honors" />
        </x-admin.nav-group>
    </nav>

    {{-- Footer --}}
    <div class="border-t border-gray-100 p-4 text-sm text-gray-500">
        <span class="sidebar-label">Logged in as <span class="text-gray-800 font-medium">{{ auth()->user()->name }}</span></span>
        <span class="block text-xs text-gray-400 mt-0.5 sidebar-label">Teacher</span>
    </div>
</aside>

{{-- Mobile overlay --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 bg-black bg-opacity-40 z-30 hidden md:hidden"
    onclick="toggleSidebar()"
></div>