<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(): View
    {
        $teachers = Teacher::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            // Step 1 — create the user account
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'teacher',
            ]);

            // Step 2 — create the teacher profile linked to the user
            Teacher::create([
                'user_id'       => $user->id,
                'employee_id'   => $request->employee_id,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender'        => $request->gender,
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher account created successfully.');
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load('user', 'classes.academicYear', 'classes.grade');

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        $teacher->load('user');

        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(UpdateTeacherRequest $request, Teacher $teacher): RedirectResponse
    {
        DB::transaction(function () use ($request, $teacher) {
            // Step 1 — update user account
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
            ];

            // Only update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $teacher->user->update($userData);

            // Step 2 — update teacher profile
            $teacher->update([
                'employee_id'   => $request->employee_id,
                'phone'         => $request->phone,
                'address'       => $request->address,
                'date_of_birth' => $request->date_of_birth,
                'gender'        => $request->gender,
            ]);
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        // Block deletion if teacher has active class assignments
        if ($teacher->classes()->exists()) {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', 'Cannot delete this teacher because they are assigned to classes.');
        }

        DB::transaction(function () use ($teacher) {
            // Soft delete both the profile and the user account
            $teacher->user->delete();
            $teacher->delete();
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher deactivated successfully.');
    }
}
