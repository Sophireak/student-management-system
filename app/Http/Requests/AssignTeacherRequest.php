<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $classId = $this->route('class')->id;

        return [
            'teacher_id' => [
                'required',
                'exists:teachers,id',
                // Prevent duplicate assignment to same class
                Rule::unique('class_teachers')
                    ->where(fn($q) => $q->where('class_id', $classId)),
            ],
            'is_primary' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'teacher_id.exists' =>
            'The selected teacher does not exist.',
            'teacher_id.unique' =>
            'This teacher is already assigned to this class.',
        ];
    }
}
