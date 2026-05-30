<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:50', 'unique:grades,name'],
            'level' => ['required', 'integer', 'min:1', 'unique:grades,level'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique'  => 'A grade with this name already exists.',
            'level.unique' => 'A grade with this level already exists.',
        ];
    }
}
