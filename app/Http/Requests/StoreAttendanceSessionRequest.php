<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'class_id'     => ['required', 'exists:classes,id'],
            'subject_id'   => ['required', 'exists:subjects,id'],
            'session_date' => [
                'required',
                'date',
                'before_or_equal:today',
                // One session per class + subject + date + period
                Rule::unique('attendance_sessions')
                    ->where(fn($q) => $q
                        ->where('class_id',   $this->class_id)
                        ->where('subject_id', $this->subject_id)
                        ->where('session_date', $this->session_date)
                        ->where('period',     $this->period ?? null)
                    ),
            ],
            'period' => ['nullable', 'in:morning,afternoon'],
            'topic'  => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_date.before_or_equal' =>
                'Session date cannot be in the future.',
            'session_date.unique' =>
                'An attendance session already exists for this class, subject, date and period.',
        ];
    }
}