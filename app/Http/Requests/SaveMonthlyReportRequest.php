<?php

namespace App\Http\Requests;

use App\Models\MonthlyReportLock;
use Illuminate\Foundation\Http\FormRequest;

class SaveMonthlyReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTeacher();
    }

    public function rules(): array
    {
        return [
            'class_id'         => ['required', 'exists:classes,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'month'            => ['required', 'integer', 'min:1', 'max:9'],
            'scores'           => ['required', 'array'],

            // Numeric score
            'scores.*.enrollment_id' => ['required', 'exists:enrollments,id'],
            'scores.*.subject_id'    => ['required', 'exists:subjects,id'],
            'scores.*.score'         => ['nullable', 'numeric', 'min:0', 'max:100'],
            'scores.*.grade'         => [
                'nullable',
                'string',
                'in:Good,Satisfactory,Needs Improvement',
            ],
            'scores.*.pass_fail'     => [
                'nullable',
                'string',
                'in:Pass,Fail',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'month.min'              => 'Month must be between 1 and 9.',
            'month.max'              => 'Month must be between 1 and 9.',
            'scores.*.score.min'     => 'Score cannot be negative.',
            'scores.*.score.max'     => 'Score cannot exceed 100.',
            'scores.*.grade.in'      => 'Grade must be Good, Satisfactory, or Needs Improvement.',
            'scores.*.pass_fail.in'  => 'Value must be Pass or Fail.',
        ];
    }

    // Check lock status before validation passes
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $locked = MonthlyReportLock::where('class_id', $this->class_id)
                ->where('academic_year_id', $this->academic_year_id)
                ->where('month', $this->month)
                ->exists();

            if ($locked && ! auth()->user()->isAdmin()) {
                $validator->errors()->add(
                    'month',
                    'This monthly report has been locked by the administrator.'
                );
            }
        });
    }
}