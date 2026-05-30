<?php

namespace App\Http\Requests;

use App\Models\AnnualReportLock;
use Illuminate\Foundation\Http\FormRequest;

class SaveAnnualReportRequest extends FormRequest
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
            'scores'           => ['required', 'array'],

            'scores.*.enrollment_id'   => ['required', 'exists:enrollments,id'],
            'scores.*.semester1_avg'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'scores.*.semester2_avg'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'scores.*.semester1_conduct' => [
                'nullable', 'string',
                'in:Good,Satisfactory,Needs Improvement',
            ],
            'scores.*.semester2_conduct' => [
                'nullable', 'string',
                'in:Good,Satisfactory,Needs Improvement',
            ],
            'scores.*.is_passing' => ['nullable', 'boolean'],
            'scores.*.notes'      => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $locked = AnnualReportLock::where('class_id', $this->class_id)
                ->where('academic_year_id', $this->academic_year_id)
                ->exists();

            if ($locked) {
                $validator->errors()->add(
                    'class_id',
                    'This annual report has been locked and cannot be edited.'
                );
            }
        });
    }
}