@props(['classes', 'academicYears' => collect(), 'routePrefix',
        'selectedClassId' => null, 'selectedYearId' => null])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 mb-4">
    <form method="GET"
          action="{{ route($routePrefix . '.annual-report.show') }}">

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            @if ($academicYears->isNotEmpty())
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">
                        Academic Year
                    </label>
                    <select name="academic_year_id"
                            class="w-full border border-gray-300 rounded-md
                                   px-3 py-2 text-sm focus:outline-none
                                   focus:ring-2 focus:ring-blue-500">
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
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Class
                </label>
                <select name="class_id"
                        class="w-full border border-gray-300 rounded-md
                               px-3 py-2 text-sm focus:outline-none
                               focus:ring-2 focus:ring-blue-500">
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
                        class="w-full px-4 py-2 bg-blue-600 text-white text-sm
                               font-medium rounded-md hover:bg-blue-700">
                    Load Sheet
                </button>
            </div>
        </div>
    </form>
</div>