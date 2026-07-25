@props(['classes', 'academicYears' => collect(), 'routePrefix',
        'selectedClassId' => null, 'selectedYearId' => null])

<div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">
        Select Annual Report
    </h3>

    <form method="GET"
          action="{{ route($routePrefix . '.annual-report.show') }}">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            @if ($academicYears->isNotEmpty())
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">
                        Academic Year
                    </label>
                    <select name="academic_year_id"
                            class="w-full border border-gray-300 rounded-full
                                   px-4 py-2.5 text-sm bg-gray-50 focus:outline-none
                                   focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white">
                        <option value="">— Select Year —</option>
                        @foreach ($academicYears as $year)
                            <option value="{{ $year->id }}"
                                {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                                {{ $year->is_active ? '(Active)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <input type="hidden" name="academic_year_id"
                       value="{{ $selectedYearId }}">
            @endif

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">
                    Class
                </label>
                <select name="class_id"
                        class="w-full border border-gray-300 rounded-full
                               px-4 py-2.5 text-sm bg-gray-50 focus:outline-none
                               focus:ring-2 focus:ring-green-500 focus:border-green-500 focus:bg-white">
                    <option value="">— Select Class —</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}"
                            {{ $selectedClassId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }} — {{ $class->grade->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm
                               font-semibold rounded-full hover:bg-green-700 transition-colors">
                    <i class="ti ti-eye text-base"></i> Load Sheet
                </button>
            </div>
        </div>
    </form>
</div>
