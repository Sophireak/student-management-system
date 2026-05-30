<?php

namespace App\Http\Requests;

use App\Models\SemesterReportLock;
use Illuminate\Foundation\Http\FormRequest;

class SaveSemesterReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'semester'         => ['required', 'integer', 'in:1,2'],
            'scores'           => ['required', 'array'],

            'scores.*.enrollment_id' => ['required', 'exists:enrollments,id'],
            'scores.*.subject_id'    => ['required', 'exists:subjects,id'],
            'scores.*.score'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'scores.*.grade'         => [
                'nullable', 'string',
                'in:Good,Satisfactory,Needs Improvement',
            ],
            'scores.*.pass_fail' => [
                'nullable', 'string', 'in:Pass,Fail',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $locked = SemesterReportLock::where('class_id', $this->class_id)
                ->where('academic_year_id', $this->academic_year_id)
                ->where('semester', $this->semester)
                ->exists();

            if ($locked) {
                $validator->errors()->add(
                    'semester',
                    'This semester report has been locked.'
                );
            }
        });
    }
}