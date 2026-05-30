<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Array of attendance records keyed by enrollment_id
            'attendance'                => ['required', 'array'],
            'attendance.*.enrollment_id' => [
                'required',
                'exists:enrollments,id',
            ],
            'attendance.*.status' => [
                'required',
                'in:present,absent,late,excused',
            ],
            'attendance.*.notes' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendance.required'              => 'No attendance data submitted.',
            'attendance.*.status.required'     => 'Status is required for each student.',
            'attendance.*.status.in'           =>
            'Status must be present, absent, late, or excused.',
        ];
    }
}
