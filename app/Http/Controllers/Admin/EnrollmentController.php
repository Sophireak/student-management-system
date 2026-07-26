<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Http\Requests\UpdateEnrollmentRequest;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function index(Request $request)
{
    $query = Enrollment::with(['student', 'schoolClass.grade', 'schoolClass.academicYear']);

    // Search
    if ($search = $request->search) {
        $query->whereHas('student', function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('student_id', 'like', "%{$search}%");
        });
    }

    // Status filter
    if ($status = $request->status) {
        $query->where('status', $status);
    }

    $enrollments = $query->latest('enrolled_at')->paginate(20)->withQueryString();

    // Counts for tabs
    $totalCount       = Enrollment::count();
    $activeCount      = Enrollment::where('status', 'active')->count();
    $transferredCount = Enrollment::where('status', 'transferred')->count();
    $droppedCount     = Enrollment::where('status', 'dropped')->count();

    return view('admin.enrollments.index', compact(
        'enrollments', 'search', 'status',
        'totalCount', 'activeCount', 'transferredCount', 'droppedCount'
    ));
}

    public function create(): View
    {
        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
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
        ]);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    public function edit(Enrollment $enrollment): View
    {
        $enrollment->load(['student', 'schoolClass.grade', 'schoolClass.academicYear']);

        $classes = SchoolClass::with(['grade', 'academicYear'])
            ->orderBy('grade_id')
            ->orderBy('name')
            ->get();

        return view('admin.enrollments.edit', compact('enrollment', 'classes'));
    }

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

    public function transfer(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        if ($request->class_id == $enrollment->class_id) {
            return back()->with('error', 'Student is already in this class.');
        }

        $exists = Enrollment::where('student_id', $enrollment->student_id)
            ->where('class_id', $request->class_id)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Student already has an active enrollment in that class.');
        }

        $enrollment->update(['status' => 'transferred']);

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

    public function promote(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
        ]);

        $exists = Enrollment::where('student_id', $enrollment->student_id)
            ->where('class_id', $request->class_id)
            ->where('status', 'active')
            ->exists();

        if ($exists) {
            return back()->with('error', 'Student already has an active enrollment in that class.');
        }

        $enrollment->update(['status' => 'transferred']);

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
