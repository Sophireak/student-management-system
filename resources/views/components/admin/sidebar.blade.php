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

        <x-admin.nav-item route="admin.dashboard" icon="ti ti-layout-dashboard" label="Dashboard" />

        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Academic</div>
        <x-admin.nav-item route="admin.academic-years.index" icon="ti ti-calendar"   label="Academic Years" />
        <x-admin.nav-item route="admin.grades.index"         icon="ti ti-award"      label="Grades" />
        <x-admin.nav-item route="admin.subjects.index"       icon="ti ti-book"       label="Subjects" />

        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">People</div>
        <x-admin.nav-item route="admin.teachers.index" icon="ti ti-school" label="Teachers" />
        <x-admin.nav-item route="admin.students.index" icon="ti ti-users"  label="Students" />

        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Classes</div>
        <x-admin.nav-item route="admin.classes.index"     icon="ti ti-building"       label="Classes" />
        <x-admin.nav-item route="admin.enrollments.index" icon="ti ti-clipboard-list" label="Enrollments" />

        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Attendance</div>
        <x-admin.nav-item route="admin.attendance-sessions.index" icon="ti ti-calendar-check" label="Attendance" />

        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Scores</div>
        <x-admin.nav-item route="admin.examination-scores.index" icon="ti ti-chart-bar" label="Examination Scores" />

        <div class="pt-4 pb-1 px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</div>
        <x-admin.nav-item route="admin.score-report.index" icon="ti ti-file-analytics" label="Score Reports" />

    </nav>

    {{-- Footer --}}
    <div class="border-t border-gray-100 px-5 py-3">
        <p class="text-xs text-gray-400">Logged in as</p>
        <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->name }}</p>
    </div>
</aside>
