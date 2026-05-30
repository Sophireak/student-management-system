<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveScoreSheetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isTeacher();
    }

    public function rules(): array
    {
        return [
            'exam_session_id'        => ['required', 'exists:exam_sessions,id'],
            'scores'                 => ['required', 'array'],
            'scores.*.enrollment_id' => ['required', 'exists:enrollments,id'],
            'scores.*.subject_id'    => ['required', 'exists:subjects,id'],
            'scores.*.score'         => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'exam_session_id.required' => 'Exam session is required.',
            'exam_session_id.exists'   => 'The selected exam session does not exist.',
            'scores.required'          => 'No score data submitted.',
            'scores.*.score.numeric'   => 'Score must be a number.',
            'scores.*.score.min'       => 'Score cannot be negative.',
        ];
    }
}