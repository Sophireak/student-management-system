<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubjectController extends Controller
{
    public function index(Request $request): View
    {
        $search  = $request->input('search');
        $gradeId = $request->input('grade_id');

        $subjects = Subject::with('grade')
            ->when($search, fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
            )
            ->when($gradeId, fn($q) => $q->where('grade_id', $gradeId))
            ->orderBy('grade_id')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $grades = Grade::orderBy('level')->get();

        return view('admin.subjects.index', compact('subjects', 'grades', 'search', 'gradeId'));
    }

    public function create(): View
    {
        $grades = Grade::orderBy('level')->get();
        return view('admin.subjects.create', compact('grades'));
    }

    public function store(StoreSubjectRequest $request): RedirectResponse
    {
        Subject::create($request->validated());

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject created successfully.');
    }

    public function edit(Subject $subject): View
    {
        $grades = Grade::orderBy('level')->get();
        return view('admin.subjects.edit', compact('subject', 'grades'));
    }

    public function update(UpdateSubjectRequest $request, Subject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject updated successfully.');
    }

    public function destroy(Subject $subject): RedirectResponse
    {
        $subject->delete();

        return redirect()
            ->route('admin.subjects.index')
            ->with('success', 'Subject deleted successfully.');
    }

    public function show(Subject $subject): RedirectResponse
    {
        return redirect()->route('admin.subjects.index');
    }
  public function storeTemplate(Request $request): RedirectResponse
{
    $gradeId = $request->input('grade_id');

    if (!$gradeId) {
        return redirect()
            ->route('admin.subjects.index')
            ->with('error', 'Please select a grade to apply the template.');
    }

    $grade = Grade::findOrFail($gradeId);

    $subjects = [
        'សរសេរតាមអាន',
        'អំណានយល់ន័យ',
        'តែងសេចក្តី',
        'គណិតវិទ្យា',
        'វិទ្យាសាស្ត្រអនុវត្ត',
        'សិក្សាសង្គម',
        'អប់រំកាយ សុខភាព កីឡា',
        'ស្តាប់',
        'និយាយ',
        'គំនូរ',
        'អក្សរផ្ចង់',
        'ភាសាបរទេស',
        'វិន័យ-សីលធម៍រស់នៅ',
        'កិច្ចការផ្ទះ',
        'កីឡា-ពលកម្ម',
        'អប់រំបំណិនជីវិតតាមមូលដ្ឋាន',
    ];

    $created = 0;
    $restored = 0;
    $skipped = 0;

    foreach ($subjects as $name) {
        $name = trim($name);

        $existing = Subject::withTrashed()
            ->where('grade_id', $gradeId)
            ->where('name', $name)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
                $existing->update([
                    'score_type' => $existing->score_type ?: 'numeric',
                    'max_score'  => $existing->max_score ?: 100,
                ]);
                $restored++;
            } else {
                $skipped++;
            }

            continue;
        }

        Subject::create([
            'grade_id'   => $gradeId,
            'name'       => $name,
            'score_type' => 'numeric',
            'max_score'  => 100,
        ]);

        $created++;
    }

    $parts = [];
    if ($created > 0) $parts[] = "{$created} created";
    if ($restored > 0) $parts[] = "{$restored} restored";
    if ($skipped > 0) $parts[] = "{$skipped} skipped";

    $summary = empty($parts) ? 'No changes made' : implode(', ', $parts);

    return redirect()
        ->route('admin.subjects.index', ['grade_id' => $gradeId])
        ->with('success', "Template applied to {$grade->name}: {$summary}.");
}
}