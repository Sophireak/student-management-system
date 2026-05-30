<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\Subject;

class GradeSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['id' => 1, 'name' => 'ថ្នាក់ទី១', 'level' => 1],
            ['id' => 2, 'name' => 'ថ្នាក់ទី២', 'level' => 2],
            ['id' => 3, 'name' => 'ថ្នាក់ទី៣', 'level' => 3],
            ['id' => 4, 'name' => 'ថ្នាក់ទី៤', 'level' => 4],
            ['id' => 5, 'name' => 'ថ្នាក់ទី៥', 'level' => 5],
            ['id' => 6, 'name' => 'ថ្នាក់ទី៦', 'level' => 6],
        ];

        foreach ($grades as $grade) {
            Grade::updateOrCreate(
                ['id' => $grade['id']],
                ['name' => $grade['name'], 'level' => $grade['level']]
            );
        }

        $subjects = [
            ['name' => 'ភាសាខ្មែរ',    'score_type' => 'numeric',   'max_score' => 100],
            ['name' => 'គណិតវិទ្យា',    'score_type' => 'numeric',   'max_score' => 100],
            ['name' => 'វិទ្យាសាស្ត្រ', 'score_type' => 'numeric',   'max_score' => 100],
            ['name' => 'សិក្សាសង្គម',   'score_type' => 'numeric',   'max_score' => 100],
            ['name' => 'សីលធម៌',        'score_type' => 'grade',     'max_score' => 0],
            ['name' => 'កីឡា',          'score_type' => 'pass_fail', 'max_score' => 0],
            ['name' => 'សិល្បៈ',        'score_type' => 'pass_fail', 'max_score' => 0],
        ];

        foreach (Grade::all() as $grade) {
            foreach ($subjects as $i => $subject) {
                Subject::firstOrCreate(
                    ['grade_id' => $grade->id, 'name' => $subject['name']],
                    [
                        'code'       => 'G' . $grade->level . '-' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                        'score_type' => $subject['score_type'],
                        'max_score'  => $subject['max_score'],
                    ]
                );
            }
        }

        $this->command->info('Grades and Subjects seeded.');
    }
}
