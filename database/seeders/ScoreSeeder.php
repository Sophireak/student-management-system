<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\MonthlyScore;
use App\Models\Subject;

class ScoreSeeder extends Seeder
{
    public function run(): void
    {
        $year     = AcademicYear::where('is_active', true)->first();
        $subjects = Subject::all();

        $enrollments = Enrollment::where('status', 'active')
            ->whereHas('schoolClass', fn($q) => $q->where('academic_year_id', $year->id))
            ->get();

        $gradeOptions    = ['Good', 'Satisfactory', 'Needs Improvement'];
        $passfailOptions = ['Pass', 'Fail'];

        // Seed months 1-3 (Sep, Oct, Nov) with scores
        foreach ([1, 2, 3] as $month) {
            foreach ($enrollments as $enrollment) {
                foreach ($subjects as $subject) {
                    $existing = MonthlyScore::where([
                        'enrollment_id'    => $enrollment->id,
                        'subject_id'       => $subject->id,
                        'month'            => $month,
                        'academic_year_id' => $year->id,
                    ])->exists();

                    if ($existing) continue;

                    $data = [
                        'enrollment_id'    => $enrollment->id,
                        'subject_id'       => $subject->id,
                        'month'            => $month,
                        'academic_year_id' => $year->id,
                        'score'            => null,
                        'grade'            => null,
                        'pass_fail'        => null,
                        'entered_by'       => 1,
                    ];

                    if ($subject->score_type === 'numeric') {
                        $data['score'] = rand(40, 100);
                    } elseif ($subject->score_type === 'grade') {
                        $data['grade'] = $gradeOptions[array_rand($gradeOptions)];
                    } else {
                        $data['pass_fail'] = $passfailOptions[array_rand($passfailOptions)];
                    }

                    MonthlyScore::create($data);
                }
            }
        }

        $this->command->info('Scores seeded for months 1-3.');
    }
}