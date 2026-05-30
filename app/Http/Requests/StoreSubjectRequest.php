<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'grade_id' => ['required', 'exists:grades,id'],
            'name'     => [
                'required',
                'string',
                'max:100',
                // Unique name within the same grade only
                Rule::unique('subjects')->where(
                    fn($query) =>
                    $query->where('grade_id', $this->grade_id)
                ),
            ],
            'code' => [
                'nullable',
                'string',
                'max:20',
                'unique:subjects,code',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'grade_id.exists' => 'The selected grade does not exist.',
            'name.unique'     => 'This subject already exists for the selected grade.',
            'code.unique'     => 'This subject code is already in use.',
        ];
    }
}
