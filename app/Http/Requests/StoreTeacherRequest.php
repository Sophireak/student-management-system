<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            // User account fields
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],

            // Teacher profile fields
            'employee_id'           => ['nullable', 'string', 'max:50', 'unique:teachers,employee_id'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'address'               => ['nullable', 'string', 'max:255'],
            'date_of_birth'         => ['nullable', 'date', 'before:today'],
            'gender'                => ['nullable', 'in:male,female'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'          => 'This email address is already in use.',
            'employee_id.unique'    => 'This employee ID is already assigned.',
            'password.confirmed'    => 'The password confirmation does not match.',
            'date_of_birth.before'  => 'Date of birth must be in the past.',
        ];
    }
}
