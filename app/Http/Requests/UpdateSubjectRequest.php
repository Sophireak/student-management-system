<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $id = $this->subject->id;

        return [
            'grade_id' => ['required', 'exists:grades,id'],
            'name'     => [
                'required',
                'string',
                'max:100',
                // Unique name within same grade, ignoring self
                Rule::unique('subjects')
                    ->where(
                        fn($query) =>
                        $query->where('grade_id', $this->grade_id)
                    )
                    ->ignore($id),
            ],
            'code' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->ignore($id),
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
