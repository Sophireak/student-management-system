<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $teacher = $this->route('teacher');
        $userId  = $teacher->user_id;

        return [
            // User account fields
            'name'          => ['required', 'string', 'max:255'],
            'email'         => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            // Password is optional on update
            'password'      => ['nullable', 'string', 'min:8', 'confirmed'],

            // Teacher profile fields
            'employee_id'   => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('teachers', 'employee_id')->ignore($teacher->id),
            ],
            'phone'         => ['nullable', 'string', 'max:20'],
            'address'       => ['nullable', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender'        => ['nullable', 'in:male,female'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'         => 'This email address is already in use.',
            'employee_id.unique'   => 'This employee ID is already assigned.',
            'password.confirmed'   => 'The password confirmation does not match.',
            'date_of_birth.before' => 'Date of birth must be in the past.',
        ];
    }
}
