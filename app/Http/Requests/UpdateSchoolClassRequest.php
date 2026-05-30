<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $id = $this->route('class')->id;

        return [
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'grade_id'         => ['required', 'exists:grades,id'],
            'name'             => [
                'required',
                'string',
                'max:20',
                Rule::unique('classes')
                    ->where(
                        fn($q) =>
                        $q->where('academic_year_id', $this->academic_year_id)
                            ->where('grade_id', $this->grade_id)
                    )
                    ->ignore($id),
            ],
            'capacity' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' =>
            'A class with this name already exists for the selected grade and year.',
        ];
    }
}
