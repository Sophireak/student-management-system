<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGradeRequest;
use App\Http\Requests\UpdateGradeRequest;
use App\Models\Grade;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $grades = Grade::withCount(['subjects', 'classes'])
            ->when($search, fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('level', 'like', "%{$search}%")
            )
            ->orderBy('level')
            ->paginate(10)
            ->withQueryString();

        return view('admin.grades.index', compact('grades', 'search'));
    }

    public function create(): View
    {
        return view('admin.grades.create');
    }

    public function store(StoreGradeRequest $request): RedirectResponse
    {
        Grade::create($request->validated());

        return redirect()
            ->route('admin.grades.index')
            ->with('success', 'Grade created successfully.');
    }

    public function edit(Grade $grade): View
    {
        return view('admin.grades.edit', compact('grade'));
    }

    public function update(UpdateGradeRequest $request, Grade $grade): RedirectResponse
    {
        $grade->update($request->validated());

        return redirect()
            ->route('admin.grades.index')
            ->with('success', 'Grade updated successfully.');
    }

    public function destroy(Grade $grade): RedirectResponse
    {
        if ($grade->subjects()->exists()) {
            return redirect()
                ->route('admin.grades.index')
                ->with('error', 'Cannot delete this grade because it has subjects assigned.');
        }

        if ($grade->classes()->exists()) {
            return redirect()
                ->route('admin.grades.index')
                ->with('error', 'Cannot delete this grade because it has classes assigned.');
        }

        $grade->delete();

        return redirect()
            ->route('admin.grades.index')
            ->with('success', 'Grade deleted successfully.');
    }

    public function show(Grade $grade): RedirectResponse
    {
        return redirect()->route('admin.grades.index');
    }
}