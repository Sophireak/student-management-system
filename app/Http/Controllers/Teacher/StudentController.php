<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Get all student IDs belonging to the authenticated teacher's classes.
     */
    private function getTeacherStudentIds(): array
    {
        return auth()->user()
            ->teacher
            ->classes
            ->flatMap(fn($class) => $class->enrollments->pluck('student_id'))
            ->unique()
            ->toArray();
    }

    /**
     * Get the teacher's classes with enrollments preloaded (for filter dropdown).
     */
    private function getTeacherClasses()
    {
        return auth()->user()
            ->teacher
            ->classes()
            ->with(['grade', 'academicYear', 'enrollments'])
            ->get();
    }

    public function index(Request $request): View
    {
        $search  = $request->input('search');
        $classId = $request->input('class_id');

        $teacher  = auth()->user()->teacher;
        $classes  = $this->getTeacherClasses();

        // Build student IDs scoped to selected class or all teacher classes
        $studentIds = $classId
            ? $classes->firstWhere('id', $classId)
                ?->enrollments->pluck('student_id')->toArray() ?? []
            : $this->getTeacherStudentIds();

        $students = Student::whereIn('id', $studentIds)
            ->when($search, fn($q) =>
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$search}%"])
                  ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$search}%"])
            )
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return view('teacher.students.index', compact('students', 'search', 'classes', 'classId'));
    }

    public function show(Student $student): View
    {
        abort_unless(
            in_array($student->id, $this->getTeacherStudentIds()),
            403,
            'This student is not in your assigned classes.'
        );

        $student->load([
            'enrollments.schoolClass.grade',
            'enrollments.schoolClass.academicYear',
        ]);

        return view('teacher.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        abort_unless(
            in_array($student->id, $this->getTeacherStudentIds()),
            403,
            'This student is not in your assigned classes.'
        );

        return view('teacher.students.edit', compact('student'));
    }

    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        abort_unless(
            in_array($student->id, $this->getTeacherStudentIds()),
            403,
            'This student is not in your assigned classes.'
        );

        $student->update($request->validated());

        return redirect()
            ->route('teacher.students.index')
            ->with('success', "{$student->full_name} updated successfully.");
    }
}