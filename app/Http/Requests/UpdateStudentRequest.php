<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'first_name'    => ['required', 'string', 'max:100'],
            'last_name'     => ['required', 'string', 'max:100'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender'        => ['nullable', 'in:male,female'],
            'phone'         => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string', 'max:255'],

            'guardian_name'         => ['nullable', 'string', 'max:100'],
            'guardian_phone'        => ['nullable', 'string', 'max:20'],
            'guardian_relationship' => ['nullable', 'in:father,mother,other'],
        ];
    }

    public function messages(): array
    {
        return [
            'date_of_birth.before'     => 'Date of birth must be in the past.',
            'guardian_relationship.in' => 'Relationship must be father, mother, or other.',
        ];
    }
}
