{{-- 
    MOBILE SLIDE PANEL
    Slides in from the right when avatar is tapped on mobile
    Only visible on mobile (md:hidden)
--}}
<div
    x-show="mobileMenu"
    x-cloak
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full"
    x-transition:enter-end="translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-x-0"
    x-transition:leave-end="translate-x-full"
    class="fixed inset-y-0 right-0 z-50 w-80 max-w-[85vw]
           bg-white shadow-2xl flex flex-col
           md:hidden"
>
    {{-- ========================
         HEADER — User Info
         ======================== --}}
    <div class="px-5 py-5 bg-gradient-to-br from-slate-800 to-slate-900 text-white flex-shrink-0">
        <div class="flex items-center justify-between mb-4">
            <span class="text-xs font-bold text-white/40 uppercase tracking-widest">
                Menu
            </span>
            <button
                @click="closeMenu()"
                class="w-8 h-8 rounded-lg flex items-center justify-center
                       text-white/60 hover:text-white hover:bg-white/10
                       transition-colors"
            >
                <i class="ti ti-x text-lg"></i>
            </button>
        </div>

        <div class="flex items-center gap-3">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatarUrl() }}" 
                     alt="{{ auth()->user()->name }}"
                     class="w-14 h-14 rounded-full object-cover 
                            ring-2 ring-white/20 shadow-lg">
            @else
                <span class="inline-flex items-center justify-center 
                             w-14 h-14 rounded-full 
                             bg-white/10 text-white font-bold text-lg 
                             ring-2 ring-white/20 shadow-lg">
                    {{ auth()->user()->initials() }}
                </span>
            @endif

            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold truncate">
                    {{ auth()->user()->name }}
                </p>
                <p class="text-xs text-white/60 truncate">
                    {{ auth()->user()->email }}
                </p>
                <span class="inline-flex items-center gap-1 mt-1.5 px-2 py-0.5 
                             rounded-full text-[10px] font-bold uppercase 
                             bg-white/10 text-white/80">
                    <i class="ti ti-{{ auth()->user()->isAdmin() ? 'shield-check' : 'school' }} text-[10px]"></i>
                    {{ auth()->user()->isAdmin() ? 'Administrator' : 'គ្រូបង្រៀន' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ========================
         NAVIGATION
         ======================== --}}
    <div class="flex-1 overflow-y-auto">

        {{-- Daily Tasks --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                Daily Tasks
            </p>
        </div>

        <div class="px-3">
            <a href="{{ route('admin.dashboard') }}" @click="closeMenu()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium 
                      {{ request()->routeIs('admin.dashboard') 
                          ? 'bg-green-50 text-green-700' 
                          : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                <i class="ti ti-layout-dashboard text-lg {{ request()->routeIs('admin.dashboard') ? 'text-green-600' : 'text-gray-400' }}"></i>
                Dashboard
            </a>

            <a href="{{ route('admin.student-attendance.index') }}" @click="closeMenu()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium 
                      {{ request()->routeIs('admin.student-attendance.*') 
                          ? 'bg-green-50 text-green-700' 
                          : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                <i class="ti ti-calendar-check text-lg {{ request()->routeIs('admin.student-attendance.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                Take Attendance
            </a>

            <a href="{{ route('admin.scores.index') }}" @click="closeMenu()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium 
                      {{ request()->routeIs('admin.scores.*') 
                          ? 'bg-green-50 text-green-700' 
                          : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                <i class="ti ti-pencil text-lg {{ request()->routeIs('admin.scores.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                Enter Scores
            </a>
        </div>

        {{-- Management --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                Management
            </p>
        </div>

        <div class="px-3">
            <a href="{{ route('admin.students.index') }}" @click="closeMenu()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium 
                      {{ request()->routeIs('admin.students.*') 
                          ? 'bg-green-50 text-green-700' 
                          : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                <i class="ti ti-users text-lg {{ request()->routeIs('admin.students.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                Students
            </a>

            <a href="{{ route('admin.teachers.index') }}" @click="closeMenu()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium 
                      {{ request()->routeIs('admin.teachers.*') 
                          ? 'bg-green-50 text-green-700' 
                          : 'text-gray-700 hover:bg-gray-50' }} transition-colors">
                <i class="ti ti-school text-lg {{ request()->routeIs('admin.teachers.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                Teachers
            </a>
        </div>

        {{-- Reports --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                Reports
            </p>
        </div>

        <div x-data="{ reportsOpen: false }" class="px-3">
            <button @click="reportsOpen = !reportsOpen"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm 
                           font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="ti ti-chart-bar text-gray-400 text-lg"></i>
                <span class="flex-1 text-left">Reports</span>
                <i class="ti ti-chevron-down text-gray-400 text-sm transition-transform"
                   :class="reportsOpen ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="reportsOpen" x-collapse class="ml-3 border-l-2 border-gray-100">
                <a href="{{ route('admin.monthly-report.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.monthly-report.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-calendar-stats text-sm {{ request()->routeIs('admin.monthly-report.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Monthly Report
                </a>
                <a href="{{ route('admin.semester-report.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.semester-report.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-calendar-due text-sm {{ request()->routeIs('admin.semester-report.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Semester Report
                </a>
                <a href="{{ route('admin.annual-report.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.annual-report.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-calendar-event text-sm {{ request()->routeIs('admin.annual-report.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Annual Report
                </a>
                <a href="{{ route('admin.reports.ranking.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.reports.ranking.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-medal text-sm {{ request()->routeIs('admin.reports.ranking.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Ranking
                </a>
                <a href="{{ route('admin.reports.honors.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.reports.honors.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-trophy text-sm {{ request()->routeIs('admin.reports.honors.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Honors
                </a>
            </div>
        </div>

        {{-- Academic Setup --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                Academic Setup
            </p>
        </div>

        <div x-data="{ setupOpen: false }" class="px-3">
            <button @click="setupOpen = !setupOpen"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm 
                           font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="ti ti-settings-2 text-gray-400 text-lg"></i>
                <span class="flex-1 text-left">Academic Setup</span>
                <i class="ti ti-chevron-down text-gray-400 text-sm transition-transform"
                   :class="setupOpen ? 'rotate-180' : ''"></i>
            </button>

            <div x-show="setupOpen" x-collapse class="ml-3 border-l-2 border-gray-100">
                <a href="{{ route('admin.academic-years.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.academic-years.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-calendar text-sm {{ request()->routeIs('admin.academic-years.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Academic Years
                </a>
                <a href="{{ route('admin.classes.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.classes.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-building text-sm {{ request()->routeIs('admin.classes.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Classes
                </a>
                <a href="{{ route('admin.subjects.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.subjects.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-book text-sm {{ request()->routeIs('admin.subjects.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Subjects
                </a>
                <a href="{{ route('admin.grades.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.grades.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-award text-sm {{ request()->routeIs('admin.grades.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Grades
                </a>
                <a href="{{ route('admin.enrollments.index') }}" @click="closeMenu()"
                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-600 
                          hover:bg-gray-50 rounded-lg ml-2 transition-colors
                          {{ request()->routeIs('admin.enrollments.*') ? 'text-green-700 font-semibold' : '' }}">
                    <i class="ti ti-clipboard-list text-sm {{ request()->routeIs('admin.enrollments.*') ? 'text-green-600' : 'text-gray-400' }}"></i>
                    Enrollments
                </a>
            </div>
        </div>

        {{-- Account --}}
        <div class="px-5 pt-4 pb-1">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                Account
            </p>
        </div>

        <div class="px-3">
            <a href="{{ route('profile.show') }}" @click="closeMenu()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium 
                      text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="ti ti-user text-gray-400 text-lg"></i>
                My Profile
            </a>

            <a href="#" @click="closeMenu()"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium 
                      text-gray-700 hover:bg-gray-50 transition-colors">
                <i class="ti ti-settings text-gray-400 text-lg"></i>
                Settings
            </a>
        </div>
    </div>

    {{-- ========================
         FOOTER — Logout
         ======================== --}}
    <div class="border-t border-gray-200 p-3 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-3 py-3
                           rounded-xl text-sm font-semibold text-red-600
                           hover:bg-red-50 transition-colors">
                <i class="ti ti-logout text-red-500 text-lg"></i>
                Log out
            </button>
        </form>
    </div>

</div>