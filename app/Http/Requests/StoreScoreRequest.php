<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'scores'                    => ['required', 'array'],
            'scores.*.enrollment_id'    => ['required', 'exists:enrollments,id'],
            'scores.*.score'            => [
                'required',
                'numeric',
                'min:0',
            ],
            'scores.*.remarks'          => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'scores.required'                => 'No score data submitted.',
            'scores.*.score.required'        => 'Score is required for each student.',
            'scores.*.score.numeric'         => 'Score must be a number.',
            'scores.*.score.min'             => 'Score cannot be negative.',
        ];
    }
}