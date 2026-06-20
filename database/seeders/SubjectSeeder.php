<?php

namespace Database\Seeders;

use App\Models\Grade;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('scores')->truncate();
        DB::table('exam_sessions')->truncate();
        DB::table('subjects')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $subjects = [
            ['name' => 'សរសេរតាមអាន',                'code' => 'KH-01'],
            ['name' => 'អំណានយល់ន័យ',                'code' => 'KH-02'],
            ['name' => 'តែងសេចក្តី',                 'code' => 'KH-03'],
            ['name' => 'គណិត',                       'code' => 'MA-01'],
            ['name' => 'វិទ្យាសាស្ត្រអនុវត្ត',       'code' => 'SC-01'],
            ['name' => 'សិក្សាសង្គម',                'code' => 'SS-01'],
            ['name' => 'អប់រំកាយ សុខភាព កីឡា',      'code' => 'PE-01'],
            ['name' => 'ស្តាប់',                     'code' => 'CL-01'],
            ['name' => 'និយាយ',                      'code' => 'CL-02'],
            ['name' => 'គំនូរ',                      'code' => 'CL-03'],
            ['name' => 'អក្សរផ្ចង់',                 'code' => 'CL-04'],
            ['name' => 'ភាសាបរទេស',                 'code' => 'CL-05'],
            ['name' => 'វិន័យ-សីលធម៍រស់នៅ',         'code' => 'CL-06'],
            ['name' => 'កិច្ចការផ្ទះ',                'code' => 'CL-07'],
            ['name' => 'កីឡា-ពលកម្ម',                'code' => 'CL-08'],
            ['name' => 'អប់រំបំណិនជីវិតតាមមូលដ្ឋាន', 'code' => 'LS-01'],
        ];

        $grades = Grade::orderBy('level')->get();

        foreach ($grades as $grade) {
            foreach ($subjects as $subject) {
                Subject::create([
                    'grade_id'   => $grade->id,
                    'name'       => $subject['name'],
                    'code'       => $subject['code'],
                    'score_type' => 'numeric',
                    'max_score'  => 10,
                ]);
            }
        }

        $this->command->info('✅ ' . ($grades->count() * count($subjects)) . ' subjects seeded across ' . $grades->count() . ' grades.');
    }
}