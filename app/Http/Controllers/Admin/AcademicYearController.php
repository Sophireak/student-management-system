<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAcademicYearRequest;
use App\Http\Requests\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $academicYears = AcademicYear::latest()->paginate(10);

        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function create(): View
    {
        return view('admin.academic-years.create');
    }

    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // If this year is set active, deactivate all others first
        if (! empty($data['is_active'])) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $data['is_active'] = ! empty($data['is_active']);

        AcademicYear::create($data);

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Academic year created successfully.');
    }

    public function show(AcademicYear $academicYear): View
    {
        $academicYear->loadCount('classes');

        return view('admin.academic-years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear): View
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $academicYear->update($request->validated());

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear): RedirectResponse
    {
        // Block deletion if classes are attached
        if ($academicYear->classes()->exists()) {
            return redirect()
                ->route('admin.academic-years.index')
                ->with('error', 'Cannot delete this academic year because it has classes assigned.');
        }

        $academicYear->delete();

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', 'Academic year deleted successfully.');
    }

    // Dedicated activate action — clean separation from update
    public function activate(AcademicYear $academicYear): RedirectResponse
    {
        // Deactivate all, then activate the selected one
        AcademicYear::where('is_active', true)->update(['is_active' => false]);

        $academicYear->update(['is_active' => true]);

        return redirect()
            ->route('admin.academic-years.index')
            ->with('success', "{$academicYear->name} is now the active academic year.");
    }
}