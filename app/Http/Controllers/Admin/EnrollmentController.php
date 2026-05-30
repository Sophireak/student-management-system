<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\AcademicYear;

class EnrollmentController extends Controller
{
    public function index(): View
    {
        $enrollments = Enrollment::with([
            'student',
            'schoolClass.grade',
            'schoolClass.academicYear',
        ])
            ->latest()
            ->paginate(20);

        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function create(): View
    {
        // Only active classes selectable for new enrollments
        $classes  = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas(
                'academicYear',
                fn($q) =>
                $q->where('is_active', true)
            )
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        $students = Student::orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.enrollments.create', compact('classes', 'students'));
    }

    public function store(StoreEnrollmentRequest $request): RedirectResponse
    {
        Enrollment::create([
            'student_id'  => $request->student_id,
            'class_id'    => $request->class_id,
            'enrolled_at' => $request->enrolled_at,
            'status'      => 'active',
        ]);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Student enrolled successfully.');
    }

    public function show(Enrollment $enrollment): View
    {
        $enrollment->load([
            'student',
            'schoolClass.grade',
            'schoolClass.academicYear',
            'scores.examSession.subject',
            'attendances.attendanceSession.subject',
        ]);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    // Full edit not allowed — only status change is permitted
    public function edit(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'schoolClass.grade', 'schoolClass.academicYear']);

        return view('admin.enrollments.edit', compact('enrollment'));
    }

    // Update redirects to status route — edit form only changes status
    public function update(UpdateEnrollmentRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $enrollment->update(['status' => $request->status]);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Enrollment status updated.');
    }

    public function updateStatus(UpdateEnrollmentRequest $request, Enrollment $enrollment): RedirectResponse
    {
        $enrollment->update(['status' => $request->status]);

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Enrollment status updated.');
    }

    public function destroy(Enrollment $enrollment): RedirectResponse
    {
        if ($enrollment->scores()->exists()) {
            return redirect()
                ->route('admin.enrollments.index')
                ->with('error', 'Cannot delete this enrollment because it has scores recorded.');
        }

        if ($enrollment->attendances()->exists()) {
            return redirect()
                ->route('admin.enrollments.index')
                ->with('error', 'Cannot delete this enrollment because it has attendance records.');
        }

        $enrollment->delete();

        return redirect()
            ->route('admin.enrollments.index')
            ->with('success', 'Enrollment removed successfully.');
    }
    // ─── ADD these two methods to EnrollmentController ───────────────
// Also add these use statements at the top if not already there:
// use App\Models\AcademicYear;

    /**
     * Transfer student to a different class in the SAME academic year.
     * Old enrollment → transferred, new enrollment → active.
     */
    public function transfer(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id', 'different:current_class'],
        ]);

        // Prevent transferring to the same class
        if ($request->class_id == $enrollment->class_id) {
            return back()->with('error', 'Student is already in this class.');
        }

        // Prevent duplicate active enrollment in target class
        $exists = Enrollment::where('student_id', $enrollment->student_id)
            ->where('class_id', $request->class_id)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Student already has an active enrollment in that class.');
        }

        // Mark old enrollment as transferred
        $enrollment->update(['status' => 'transferred']);

        // Create new active enrollment
        Enrollment::create([
            'student_id'  => $enrollment->student_id,
            'class_id'    => $request->class_id,
            'enrolled_at' => now(),
            'status'      => 'active',
        ]);

        return redirect()
            ->route('admin.students.show', $enrollment->student_id)
            ->with('success', 'Student transferred successfully.');
    }

    /**
     * Promote student to a class in the NEXT academic year.
     * Old enrollment → transferred, new enrollment → active.
     */
    public function promote(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        // Prevent duplicate active enrollment in target class
        $exists = Enrollment::where('student_id', $enrollment->student_id)
            ->where('class_id', $request->class_id)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Student already has an active enrollment in that class.');
        }

        // Mark old enrollment as transferred
        $enrollment->update(['status' => 'transferred']);

        // Create new active enrollment in new year/class
        Enrollment::create([
            'student_id'  => $enrollment->student_id,
            'class_id'    => $request->class_id,
            'enrolled_at' => now(),
            'status'      => 'active',
        ]);

        return redirect()
            ->route('admin.students.show', $enrollment->student_id)
            ->with('success', 'Student promoted successfully.');
    }
}
