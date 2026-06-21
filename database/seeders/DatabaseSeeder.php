<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            GradeSubjectSeeder::class,
            ClassTeacherSeeder::class,
            StudentEnrollmentSeeder::class,
            ScoreSeeder::class,
            SubjectSeeder::class,
        ]);
    }
}