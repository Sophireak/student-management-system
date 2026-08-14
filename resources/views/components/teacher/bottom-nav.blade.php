<nav class="fixed bottom-4 left-4 right-4 z-40 
            bg-white/75 backdrop-blur-2xl
            border border-white/80
            rounded-full shadow-lg shadow-green-900/15
            pb-safe">
    
    <div class="flex items-center justify-around h-16 max-w-lg mx-auto px-2">
        
        {{-- Home --}}
        <x-teacher.nav-item 
            route="teacher.dashboard" 
            icon="ti ti-home" 
            label="Home" />

        {{-- Attendance --}}
        <x-teacher.nav-item 
            route="teacher.student-attendance.index" 
            icon="ti ti-calendar-check" 
            label="Attend"
            :matches="['teacher.student-attendance']" />

        {{-- Scores --}}
        <x-teacher.nav-item 
            route="teacher.scores.index" 
            icon="ti ti-pencil" 
            label="Scores"
            :matches="['teacher.scores']" />

        {{-- Reports --}}
        <x-teacher.nav-item 
            route="teacher.reports.index" 
            icon="ti ti-chart-bar" 
            label="Reports"
            :matches="[
                'teacher.monthly-report',
                'teacher.semester-report',
                'teacher.annual-report',
                'teacher.reports.ranking',
                'teacher.reports.honors'
            ]" />

        {{-- Students --}}
        <x-teacher.nav-item 
            route="teacher.students.index" 
            icon="ti ti-users" 
            label="Students"
            :matches="['teacher.students']" />

    </div>
</nav>
