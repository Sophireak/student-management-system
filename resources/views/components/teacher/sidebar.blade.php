<aside
    id="sidebar"
    class="w-64 bg-gray-800 text-white flex flex-col flex-shrink-0
           fixed inset-y-0 left-0 z-40 transform -translate-x-full
           transition-transform duration-200 ease-in-out
           md:relative md:translate-x-0"
>
    <div class="h-16 flex items-center justify-center border-b border-gray-700 px-4">
        <span class="text-lg font-bold tracking-wide">🏫 {{ config('app.name') }}</span>
    </div>

    <nav class="flex-1 overflow-y-auto py-4 px-2 space-y-1">

        <x-admin.nav-item route="teacher.dashboard" icon="🏠" label="Dashboard" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Attendance</div>
        <x-admin.nav-item route="teacher.student-attendance.index" icon="✅" label="Student Attendance" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Scores</div>
        <x-admin.nav-item route="teacher.examination-scores.index" icon="📝" label="Examination Scores" />

        <div class="pt-4 pb-1 px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reports</div>
        <x-admin.nav-item route="teacher.monthly-report.index"  icon="📅" label="Monthly Reports" />
        <x-admin.nav-item route="teacher.semester-report.index" icon="📊" label="Semester Reports" />
        <x-admin.nav-item route="teacher.annual-report.index"   icon="🏆" label="Annual Report" />

    </nav>

    <div class="border-t border-gray-700 p-4 text-sm text-gray-400">
        Logged in as <span class="text-white font-medium">{{ auth()->user()->name }}</span>
        <span class="block text-xs text-gray-500 mt-0.5">Teacher</span>
    </div>
</aside>

<div id="sidebar-overlay"
     class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"
     onclick="toggleSidebar()"></div>