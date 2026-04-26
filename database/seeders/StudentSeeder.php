<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\User;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Get the student user we created
        $user = User::where('email', 'student@sms.com')->first();

        Student::create([
            'user_id'       => $user->id,
            'student_code'  => 'STU0001',
            'date_of_birth' => '2000-01-15',
            'gender'        => 'Male',
            'program'       => 'Computer Science',
            'status'        => 'Active',
        ]);
    }
}