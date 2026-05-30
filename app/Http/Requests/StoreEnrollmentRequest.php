<?php

namespace App\Http\Requests;

use App\Models\Enrollment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrollmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'student_id'  => [
                'required',
                'exists:students,id',
                // No duplicate enrollment in the same class
                Rule::unique('enrollments')->where(
                    fn($q) =>
                    $q->where('class_id', $this->class_id)
                ),
            ],
            'class_id'    => ['required', 'exists:classes,id'],
            'enrolled_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.exists' => 'The selected student does not exist.',
            'student_id.unique' => 'This student is already enrolled in this class.',
            'class_id.exists'   => 'The selected class does not exist.',
        ];
    }

    // Extra business rule checked after basic validation passes
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $this->checkActiveEnrollmentForYear($validator);
            $this->checkClassCapacity($validator);
        });
    }

    private function checkActiveEnrollmentForYear($validator): void
    {
        if (! $this->student_id || ! $this->class_id) {
            return;
        }

        // Get the academic year of the selected class
        $class = \App\Models\SchoolClass::find($this->class_id);
        if (! $class) return;

        // Check if student already has an active enrollment in this academic year
        $exists = Enrollment::where('student_id', $this->student_id)
            ->where('status', 'active')
            ->whereHas(
                'schoolClass',
                fn($q) =>
                $q->where('academic_year_id', $class->academic_year_id)
            )
            ->exists();

        if ($exists) {
            $validator->errors()->add(
                'student_id',
                'This student already has an active enrollment in the selected academic year.'
            );
        }
    }

    private function checkClassCapacity($validator): void
    {
        if (! $this->class_id) return;

        $class = \App\Models\SchoolClass::withCount('enrollments')
            ->find($this->class_id);

        if (
            $class && $class->capacity &&
            $class->enrollments_count >= $class->capacity
        ) {
            $validator->errors()->add(
                'class_id',
                "This class has reached its capacity of {$class->capacity} students."
            );
        }
    }
}
