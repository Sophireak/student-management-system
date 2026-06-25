<aside
    id="sidebar"
    class="w-60 bg-white text-gray-700 flex flex-col flex-shrink-0 border-r border-gray-200
           fixed inset-y-0 left-0 z-40 transform -translate-x-full
           transition-transform duration-200 ease-in-out
           md:relative md:translate-x-0"
>
    {{-- Logo --}}
    <div class="h-16 flex items-center gap-2 px-5 border-b border-gray-100">
        <div class="w-8 h-8 rounded-lg bg-green-600 flex items-center justify-center">
            <i class="ti ti-school text-white text-base"></i>
        </div>
        <span class="text-base font-bold text-gray-800 tracking-tight">{{ config('app.name') }}</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">

        {{-- Dashboard --}}
        <x-admin.nav-item route="admin.dashboard" icon="ti ti-layout-dashboard" label="Dashboard" />

        {{-- Daily Tasks --}}
        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Daily Tasks</div>
        <x-admin.nav-item route="admin.student-attendance.index" icon="ti ti-calendar-check" label="Take Attendance" />
        <x-admin.nav-item route="admin.examination-scores.index" icon="ti ti-pencil"          label="Enter Scores" />

        {{-- Management --}}
        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Management</div>
        <x-admin.nav-item route="admin.students.index" icon="ti ti-users"  label="Students" />
        <x-admin.nav-item route="admin.teachers.index" icon="ti ti-school" label="Teachers" />

        {{-- Reports (collapsible) --}}
        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</div>
        <x-admin.nav-group
            icon="ti ti-chart-bar"
            label="Reports"
            :routes="['admin.monthly-report', 'admin.semester-report', 'admin.annual-report', 'admin.reports.ranking', 'admin.reports.honors']"
        >
            <x-admin.nav-item route="admin.monthly-report.index"  icon="ti ti-calendar-stats" label="Monthly Report" />
            <x-admin.nav-item route="admin.semester-report.index" icon="ti ti-calendar-due"   label="Semester Report" />
            <x-admin.nav-item route="admin.annual-report.index"   icon="ti ti-calendar-event" label="Annual Report" />
            <x-admin.nav-item route="admin.reports.ranking.index" icon="ti ti-medal"          label="Ranking" />
            <x-admin.nav-item route="admin.reports.honors.index"  icon="ti ti-trophy"         label="Honors" />
        </x-admin.nav-group>

        {{-- Academic Setup (collapsible) --}}
        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Academic Setup</div>
        <x-admin.nav-group
            icon="ti ti-settings-2"
            label="Academic Setup"
            :routes="['admin.classes', 'admin.subjects', 'admin.grades', 'admin.academic-years', 'admin.enrollments']"
        >
            <x-admin.nav-item route="admin.classes.index"        icon="ti ti-building"       label="Classes" />
            <x-admin.nav-item route="admin.subjects.index"       icon="ti ti-book"           label="Subjects" />
            <x-admin.nav-item route="admin.grades.index"         icon="ti ti-award"          label="Grades" />
            <x-admin.nav-item route="admin.academic-years.index" icon="ti ti-calendar"       label="Academic Years" />
            <x-admin.nav-item route="admin.enrollments.index"    icon="ti ti-clipboard-list" label="Enrollments" />
        </x-admin.nav-group>

    </nav>

    {{-- Footer --}}
    <div class="border-t border-gray-100 px-5 py-3">
        <p class="text-xs text-gray-400">Logged in as</p>
        <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</p>
    </div>
</aside>

{{-- Mobile overlay --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"
    onclick="toggleSidebar()"
></div>