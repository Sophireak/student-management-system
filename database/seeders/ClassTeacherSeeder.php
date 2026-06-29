<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\User;

class ClassTeacherSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::firstOrCreate(
    ['is_active' => true],
    [
        'name'       => '2024-2025',
        'start_date' => '2024-10-01',
        'end_date'   => '2025-07-31',
    ]
);

        // Create 6 classes — one per grade
        $classNames = ['ក', 'ខ', 'គ', 'ឃ', 'ង', 'ច'];
        foreach (Grade::all() as $grade) {
            SchoolClass::firstOrCreate(
                ['grade_id' => $grade->id, 'academic_year_id' => $year->id],
                ['name' => $grade->name . $classNames[$grade->id - 1], 'capacity' => 40]
            );
        }

        // Create 6 teachers
        $teacherData = [
            ['name' => 'សុខ ដារ៉ា',    'email' => 'dara@school.edu.kh',    'phone' => '012111001'],
            ['name' => 'ចាន់ សុភា',    'email' => 'sopha@school.edu.kh',   'phone' => '012111002'],
            ['name' => 'លី វណ្ណៈ',     'email' => 'vanna@school.edu.kh',   'phone' => '012111003'],
            ['name' => 'ហេង ច័ន្ទណា',  'email' => 'chanda@school.edu.kh',  'phone' => '012111004'],
            ['name' => 'ពៅ សុខលី',     'email' => 'sokly@school.edu.kh',   'phone' => '012111005'],
            ['name' => 'ម៉ម រតនា',     'email' => 'ratana@school.edu.kh',  'phone' => '012111006'],
        ];

        $classes = SchoolClass::where('academic_year_id', $year->id)
            ->orderBy('grade_id')->get();

        foreach ($teacherData as $i => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'     => $data['name'],
                    'password' => Hash::make('password'),
                    'role'     => 'teacher',
                ]
            );

            $teacher = Teacher::firstOrCreate(
                ['user_id' => $user->id],
                ['phone' => $data['phone']]
            );

            // Assign teacher to class
            if (isset($classes[$i])) {
                $classes[$i]->classTeachers()->firstOrCreate(
                    ['teacher_id' => $teacher->id],
                    ['is_primary' => true]
                );
            }
        }

        $this->command->info('Classes and Teachers seeded.');
    }
}