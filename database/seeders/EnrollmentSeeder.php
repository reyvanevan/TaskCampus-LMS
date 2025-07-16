<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all students and courses
        $students = User::where('role', 'student')->get();
        $courses = Course::all();

        // Enroll each student in all available courses for testing
        foreach ($students as $student) {
            foreach ($courses as $course) {
                // Check if enrollment already exists
                $existingEnrollment = Enrollment::where('user_id', $student->id)
                                               ->where('course_id', $course->id)
                                               ->first();

                if (!$existingEnrollment) {
                    Enrollment::create([
                        'user_id' => $student->id,
                        'course_id' => $course->id,
                        'status' => 'active',
                    ]);
                    
                    echo "Enrolled {$student->name} in {$course->name}\n";
                }
            }
        }
    }
}
