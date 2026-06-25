<aside
    id="sidebar"
    class="w-64 bg-gray-800 text-white flex flex-col flex-shrink-0
           fixed inset-y-0 left-0 z-40 transform -translate-x-full
           transition-all duration-200 ease-in-out
           md:relative md:translate-x-0"
>
    {{-- Logo --}}
    <div class="h-16 flex items-center justify-center gap-2 border-b border-gray-700 px-4">
        <i class="ti ti-school text-green-400 text-xl"></i>
        <span class="text-lg font-bold tracking-wide">{{ config('app.name') }}</span>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-0.5">

        {{-- Dashboard --}}
        <x-admin.nav-item route="teacher.dashboard" icon="ti ti-layout-dashboard" label="Dashboard" />

        {{-- Daily Tasks --}}
        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Daily Tasks</div>
        <x-admin.nav-item route="teacher.student-attendance.index" icon="ti ti-calendar-check" label="Take Attendance" />
        <x-admin.nav-item route="teacher.examination-scores.index" icon="ti ti-pencil"          label="Enter Scores" />

        {{-- Reports (collapsible) --}}
        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</div>
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
    <div class="border-t border-gray-700 p-4 text-sm text-gray-400">
        Logged in as <span class="text-white font-medium">{{ auth()->user()->name }}</span>
        <span class="block text-xs text-gray-500 mt-0.5">Teacher</span>
    </div>
</aside>

{{-- Mobile overlay --}}
<div
    id="sidebar-overlay"
    class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"
    onclick="toggleSidebar()"
></div>