<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TeacherController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $teachers = Teacher::with('user')
            ->when($search, fn($q) =>
                $q->whereHas('user', fn($u) =>
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                )
                ->orWhere('employee_id', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.teachers.index', compact('teachers', 'search'));
    }

    public function create(): View
    {
        return view('admin.teachers.create');
    }

    public function store(StoreTeacherRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'teacher',
            ]);

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
            $userData = [
                'name'  => $request->name,
                'email' => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $teacher->user->update($userData);

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
        if ($teacher->classes()->exists()) {
            return redirect()
                ->route('admin.teachers.index')
                ->with('error', 'Cannot delete this teacher because they are assigned to classes.');
        }

        DB::transaction(function () use ($teacher) {
            $teacher->user->delete();
            $teacher->delete();
        });

        return redirect()
            ->route('admin.teachers.index')
            ->with('success', 'Teacher deactivated successfully.');
    }
}