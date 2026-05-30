<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSchoolClassRequest;
use App\Http\Requests\UpdateSchoolClassRequest;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        $search   = $request->input('search');
        $yearId   = $request->input('academic_year_id');

        $classes = SchoolClass::with(['academicYear', 'grade'])
            ->withCount('enrollments')
            ->when($search, fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('grade', fn($q2) =>
                      $q2->where('name', 'like', "%{$search}%")
                  )
            )
            ->when($yearId, fn($q) =>
                $q->where('academic_year_id', $yearId)
            )
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('grade_id')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('admin.classes.index', compact('classes', 'academicYears', 'search', 'yearId'));
    }

    public function create(): View
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $grades        = Grade::orderBy('level')->get();

        return view('admin.classes.create', compact('academicYears', 'grades'));
    }

    public function store(StoreSchoolClassRequest $request): RedirectResponse
    {
        SchoolClass::create($request->validated());

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function show(SchoolClass $class): View
    {
        $class->load([
            'academicYear',
            'grade',
            'enrollments.student',
            'teachers.user',
        ]);

        return view('admin.classes.show', compact('class'));
    }

    public function edit(SchoolClass $class): View
    {
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        $grades        = Grade::orderBy('level')->get();

        return view('admin.classes.edit', compact('class', 'academicYears', 'grades'));
    }

    public function update(UpdateSchoolClassRequest $request, SchoolClass $class): RedirectResponse
    {
        $class->update($request->validated());

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class): RedirectResponse
    {
        if ($class->enrollments()->exists()) {
            return redirect()
                ->route('admin.classes.index')
                ->with('error', 'Cannot delete this class because it has enrollments.');
        }

        $class->delete();

        return redirect()
            ->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }
}