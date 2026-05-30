<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignTeacherRequest;
use App\Models\ClassTeacher;
use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ClassTeacherController extends Controller
{
    public function index(SchoolClass $class): View
    {
        $class->load([
            'academicYear',
            'grade',
            'classTeachers.teacher.user',
        ]);

        return view('admin.class-teachers.index', compact('class'));
    }

    public function create(SchoolClass $class): View
    {
        $class->load('academicYear', 'grade');

        // Only show teachers not already assigned to this class
        $assignedIds = $class->classTeachers()->pluck('teacher_id');

        $teachers = Teacher::with('user')
                           ->whereNotIn('id', $assignedIds)
                           ->get();

        return view('admin.class-teachers.create', compact('class', 'teachers'));
    }

    public function store(AssignTeacherRequest $request, SchoolClass $class): RedirectResponse
    {
        $isPrimary = $request->boolean('is_primary');

        // If setting as primary, demote the current primary first
        if ($isPrimary) {
            $class->classTeachers()
                  ->where('is_primary', true)
                  ->update(['is_primary' => false]);
        }

        ClassTeacher::create([
            'class_id'   => $class->id,
            'teacher_id' => $request->teacher_id,
            'is_primary' => $isPrimary,
        ]);

        return redirect()
            ->route('admin.classes.teachers.index', $class)
            ->with('success', 'Teacher assigned successfully.');
    }

    public function setPrimary(SchoolClass $class, ClassTeacher $classTeacher): RedirectResponse
    {
        // Ensure the classTeacher actually belongs to this class
        if ($classTeacher->class_id !== $class->id) {
            abort(403);
        }

        // Demote current primary
        $class->classTeachers()
              ->where('is_primary', true)
              ->update(['is_primary' => false]);

        // Promote selected
        $classTeacher->update(['is_primary' => true]);

        return redirect()
            ->route('admin.classes.teachers.index', $class)
            ->with('success', 'Primary teacher updated.');
    }

    public function destroy(SchoolClass $class, ClassTeacher $classTeacher): RedirectResponse
    {
        // Ensure belongs to this class
        if ($classTeacher->class_id !== $class->id) {
            abort(403);
        }

        // Warn if teacher has sessions in this class
        $hasExamSessions = $class->examSessions()
                                 ->whereHas('scores')
                                 ->exists();

        $hasAttendanceSessions = $class->attendanceSessions()
                                       ->whereHas('attendances')
                                       ->exists();

        // We only warn — assignment removal doesn't delete sessions
        // Sessions are linked to class+subject, not directly to teacher
        $classTeacher->delete();

        $message = 'Teacher removed from class.';

        if ($hasExamSessions || $hasAttendanceSessions) {
            $message .= ' Note: existing session records remain intact.';
        }

        return redirect()
            ->route('admin.classes.teachers.index', $class)
            ->with('success', $message);
    }
}