<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGradeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        $id = $this->grade->id;

        return [
            'name'  => ['required', 'string', 'max:50', "unique:grades,name,{$id}"],
            'level' => ['required', 'integer', 'min:1', "unique:grades,level,{$id}"],
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
