<aside
    id="sidebar"
    class="w-64 bg-gray-900 text-white flex flex-col flex-shrink-0
           fixed inset-y-0 left-0 z-40 transform -translate-x-full
           transition-transform duration-200 ease-in-out
           md:relative md:translate-x-0"
>
    <div class="h-16 flex items-center justify-center border-b border-gray-700 px-4">
        <span class="text-lg font-bold tracking-wide">🏫 {{ config('app.name') }}</span>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">

        <x-admin.nav-item route="admin.dashboard" icon="🏠" label="Dashboard" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Academic</div>
        <x-admin.nav-item route="admin.academic-years.index" icon="📅" label="Academic Years" />
        <x-admin.nav-item route="admin.grades.index"         icon="🎓" label="Grades" />
        <x-admin.nav-item route="admin.subjects.index"       icon="📚" label="Subjects" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">People</div>
        <x-admin.nav-item route="admin.teachers.index" icon="👩‍🏫" label="Teachers" />
        <x-admin.nav-item route="admin.students.index" icon="👧"  label="Students" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Classes</div>
        <x-admin.nav-item route="admin.classes.index"     icon="🏛️" label="Classes" />
        <x-admin.nav-item route="admin.enrollments.index" icon="📋" label="Enrollments" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Attendance</div>
        <x-admin.nav-item route="admin.student-attendance.index" icon="✅" label="Student Attendance" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Scores</div>
        <x-admin.nav-item route="admin.examination-scores.index" icon="📝" label="Examination Scores" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</div>
        <x-admin.nav-item route="admin.reports.ranking.index" icon="📊" label="Ranking Report" />
        <x-admin.nav-item route="admin.reports.honors.index"  icon="🏅" label="Honors Report" />

    </nav>

    <div class="border-t border-gray-700 p-4 text-sm text-gray-400">
        Logged in as <span class="text-white font-medium">{{ auth()->user()->name }}</span>
    </div>
</aside>

<div id="sidebar-overlay"
     class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"
     onclick="toggleSidebar()"></div>