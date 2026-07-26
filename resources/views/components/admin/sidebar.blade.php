<aside
    id="sidebar"
    class="hidden md:flex w-60 bg-gradient-to-b from-green-700 to-green-800 
           flex-col flex-shrink-0
           relative inset-y-0 left-0 z-40 
           transition-all duration-300 ease-in-out
           overflow-x-hidden"
>
    {{-- Logo Area --}}
    <div id="sidebar-logo-area" 
         class="h-16 flex items-center justify-between 
                border-b border-green-600/50 px-4">

        <div class="flex items-center gap-2.5 sidebar-label">
            <span class="text-2xl leading-none">🎓</span>
            <div class="flex flex-col leading-tight">
                <span class="text-sm font-extrabold text-white 
                             tracking-tight">
                    {{ config('app.name') }}
                </span>
                <span class="text-[10px] font-medium text-green-200 
                             uppercase tracking-widest">
                    School Portal
                </span>
            </div>
        </div>

        <button
            id="sidebar-toggle-btn"
            class="hidden md:flex items-center justify-center 
                   w-8 h-8 rounded-lg text-green-200 
                   hover:text-white hover:bg-white/10 
                   transition-colors"
            onclick="toggleSidebar()"
            aria-label="Collapse sidebar"
        >
            <i id="sidebar-toggle-icon" 
               class="ti ti-layout-sidebar-left-collapse text-lg"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto py-3 px-3 flex flex-col gap-0.5">

        {{-- Dashboard --}}
        <x-admin.nav-item 
            route="admin.dashboard" 
            icon="ti ti-layout-dashboard" 
            label="Dashboard" />

        {{-- Daily Tasks --}}
        <div class="sidebar-section">Daily Tasks</div>
        <x-admin.nav-item 
            route="admin.student-attendance.index" 
            icon="ti ti-calendar-check" 
            label="Take Attendance" />
        <x-admin.nav-item 
            route="admin.scores.index" 
            icon="ti ti-pencil" 
            label="Enter Scores" />

        {{-- Management --}}
        <div class="sidebar-section">Management</div>
        <x-admin.nav-item 
            route="admin.students.index" 
            icon="ti ti-users" 
            label="Students" />
        <x-admin.nav-item 
            route="admin.teachers.index" 
            icon="ti ti-school" 
            label="Teachers" />

        {{-- Reports --}}
        <div class="sidebar-section">Reports</div>
        <x-admin.nav-group
            icon="ti ti-chart-bar"
            label="Reports"
            :routes="[
                'admin.monthly-report',
                'admin.semester-report',
                'admin.annual-report',
                'admin.reports.ranking',
                'admin.reports.honors'
            ]"
        >
            <x-admin.nav-item 
                route="admin.monthly-report.index" 
                icon="ti ti-calendar-stats" 
                label="Monthly Report" />
            <x-admin.nav-item 
                route="admin.semester-report.index" 
                icon="ti ti-calendar-due" 
                label="Semester Report" />
            <x-admin.nav-item 
                route="admin.annual-report.index" 
                icon="ti ti-calendar-event" 
                label="Annual Report" />
            <x-admin.nav-item 
                route="admin.reports.ranking.index" 
                icon="ti ti-medal" 
                label="Ranking" />
            <x-admin.nav-item 
                route="admin.reports.honors.index" 
                icon="ti ti-trophy" 
                label="Honors" />
        </x-admin.nav-group>

        {{-- Academic Setup --}}
        <div class="sidebar-section">Academic Setup</div>
        <x-admin.nav-group
            icon="ti ti-settings-2"
            label="Academic Setup"
            :routes="[
                'admin.academic-years',
                'admin.classes',
                'admin.subjects',
                'admin.grades',
                'admin.enrollments'
            ]"
        >
            <x-admin.nav-item 
                route="admin.academic-years.index" 
                icon="ti ti-calendar" 
                label="Academic Years" />
            <x-admin.nav-item 
                route="admin.classes.index" 
                icon="ti ti-building" 
                label="Classes" />
            <x-admin.nav-item 
                route="admin.subjects.index" 
                icon="ti ti-book" 
                label="Subjects" />
            <x-admin.nav-item 
                route="admin.grades.index" 
                icon="ti ti-award" 
                label="Grades" />
            <x-admin.nav-item 
                route="admin.enrollments.index" 
                icon="ti ti-clipboard-list" 
                label="Enrollments" />
        </x-admin.nav-group>

    </nav>

    {{-- Sidebar Footer --}}
<div class="border-t border-green-600/50 px-3 py-4 sidebar-label">
    <div class="text-center">
        <p class="text-[10px] font-bold text-green-200/60 uppercase tracking-wider">
            KruDesk
        </p>
        <p class="text-[10px] text-green-200/40 mt-0.5">
            School Portal · v1.0
        </p>
    </div>
</div>
</aside>
