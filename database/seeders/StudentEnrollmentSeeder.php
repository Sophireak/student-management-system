<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Models\Student;

class StudentEnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $year    = AcademicYear::where('is_active', true)->first();
        $classes = SchoolClass::where('academic_year_id', $year->id)
            ->orderBy('grade_id')->get();

        $firstNames = [
            'សុវណ្ណ', 'ដារ៉ា', 'ចន្ទ', 'រតនា', 'វណ្ណៈ',
            'សុភា',  'ណារ៉ា', 'វិចិត្រ', 'ច័ន្ទ', 'ពិសី',
            'ស្រីនាត', 'សុខលី', 'ណាត', 'ផល្លា', 'ស្រីម',
        ];

        $lastNames = [
            'សុខ', 'ចាន់', 'លី', 'ហេង', 'ពៅ',
            'ម៉ម', 'ទេព', 'អ៊ុំ', 'ស៊ិន', 'ខៀវ',
        ];

        $genders       = ['male', 'female'];
        $relationships = ['father', 'mother', 'other'];
        $studentCount  = Student::withTrashed()->count();

        foreach ($classes as $class) {
            for ($i = 1; $i <= 10; $i++) {
                $studentCount++;
                $year2 = now()->year;
                $sid   = 'STU-' . $year2 . '-' . str_pad($studentCount, 3, '0', STR_PAD_LEFT);

                $student = Student::firstOrCreate(
                    ['student_id' => $sid],
                    [
                        'first_name'            => $firstNames[array_rand($firstNames)],
                        'last_name'             => $lastNames[array_rand($lastNames)],
                        'gender'                => $genders[array_rand($genders)],
                        'date_of_birth'         => now()->subYears(rand(6, 13))->format('Y-m-d'),
                        'guardian_name'         => $lastNames[array_rand($lastNames)] . ' ' . $firstNames[array_rand($firstNames)],
                        'guardian_relationship' => $relationships[array_rand($relationships)],
                        'guardian_phone'        => '01' . rand(10000000, 99999999),
                    ]
                );

                $alreadyActive = Enrollment::where('student_id', $student->id)
                    ->where('status', 'active')
                    ->exists();

                if (! $alreadyActive) {
                    Enrollment::firstOrCreate(
                        ['student_id' => $student->id, 'class_id' => $class->id],
                        ['enrolled_at' => now(), 'status' => 'active']
                    );
                }
            }
        }

        $this->command->info('Students and Enrollments seeded.');
    }
}
