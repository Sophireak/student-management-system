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

            // Photo
            'photo'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_photo'  => ['nullable', 'boolean'],

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
            'photo.image'              => 'Photo must be an image file.',
            'photo.mimes'              => 'Photo must be JPG, PNG, or WEBP.',
            'photo.max'                => 'Photo size must not exceed 2MB.',
        ];
    }
}