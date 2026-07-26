<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAcademicYearRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'       => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_years', 'name')
                    ->ignore($this->academic_year->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
            'is_active'  => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after' => 'The end date must be after the start date.',
            'name.unique'    => 'An academic year with this name already exists.',
        ];
    }
}