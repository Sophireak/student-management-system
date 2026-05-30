<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExamSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'class_id'   => ['required', 'exists:classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'name'       => ['required', 'string', 'max:100'],
            'type'       => ['required', 'in:quiz,monthly,semester,final'],
            'term'       => ['nullable', 'in:term1,term2,term3'],
            'exam_date'  => ['nullable', 'date'],
            'max_score'  => ['required', 'numeric', 'min:1', 'max:1000'],

            // Prevent duplicate: same class + subject + name + type + term
            Rule::unique('exam_sessions')->where(fn($q) =>
                $q->where('class_id',   $this->class_id)
                  ->where('subject_id', $this->subject_id)
                  ->where('name',       $this->name)
                  ->where('type',       $this->type)
                  ->whereNull('deleted_at')
            ),
        ];
    }

    public function messages(): array
    {
        return [
            'type.in'      => 'Type must be quiz, monthly, semester, or final.',
            'term.in'      => 'Term must be term1, term2, or term3.',
            'max_score.min' => 'Max score must be at least 1.',
        ];
    }
}