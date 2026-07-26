<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'grade_id'         => ['required', 'exists:grades,id'],
            'name'             => [
                'required',
                'string',
                'max:20',
                // Unique name per grade per academic year
                Rule::unique('classes')->where(
                    fn($q) =>
                    $q->where('academic_year_id', $this->academic_year_id)
                        ->where('grade_id', $this->grade_id)
                ),
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100'],
            'session_period' => ['required', 'in:morning,afternoon'],
        ];
    }

    public function messages(): array
    {
        return [
            'academic_year_id.exists' => 'The selected academic year does not exist.',
            'grade_id.exists'         => 'The selected grade does not exist.',
            'name.unique'             =>
            'A class with this name already exists for the selected grade and year.',
        ];
    }
}
