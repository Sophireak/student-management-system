<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\AcademicYear;
use App\Models\SchoolClass;

class StudentController extends Controller
{
    public function index(): View
    {
        $students = Student::orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        return view('admin.students.index', compact('students'));
    }

    public function create(): View
    {
        return view('admin.students.create');
    }

    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data               = $request->validated();
        $data['student_id'] = Student::generateStudentId();

        Student::create($data);

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student created successfully.');
    }


    public function show(Student $student): View
    {
        $student->load([
            'enrollments.schoolClass.grade',
            'enrollments.schoolClass.academicYear',
        ]);

        // For each active enrollment, load classes from the same academic year
        // (excluding the current class) for transfer
        $sameYearClasses = [];
        foreach ($student->enrollments->where('status', 'active') as $enrollment) {
            $sameYearClasses[$enrollment->id] = SchoolClass::with('grade')
                ->where('academic_year_id', $enrollment->schoolClass->academic_year_id)
                ->where('id', '!=', $enrollment->class_id)
                ->orderBy('grade_id')
                ->orderBy('name')
                ->get();
        }

        // Classes from all non-active academic years for promotion
        $activeYearId    = AcademicYear::where('is_active', true)->value('id');
        $nextYearClasses = SchoolClass::with(['grade', 'academicYear'])
            ->where('academic_year_id', '!=', $activeYearId)
            ->orderBy('academic_year_id', 'desc')
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        // If there's only one academic year, show active year classes too
        // so admin can still promote within the system
        if ($nextYearClasses->isEmpty()) {
            $nextYearClasses = SchoolClass::with(['grade', 'academicYear'])
                ->orderBy('grade_id')
                ->orderBy('name')
                ->get();
        }

        return view('admin.students.show', compact(
            'student',
            'sameYearClasses',
            'nextYearClasses'
        ));
    }

    public function edit(Student $student): View
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        // student_id is never updated — excluded from validated data
        $student->update($request->validated());

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        // Block deletion if active enrollments exist
        if ($student->enrollments()->where('status', 'active')->exists()) {
            return redirect()
                ->route('admin.students.index')
                ->with('error', 'Cannot delete a student with active enrollments.');
        }

        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student archived successfully.');
    }
}
