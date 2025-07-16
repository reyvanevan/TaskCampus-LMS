<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class ImportController extends Controller
{
    public function index()
    {
        return view('import.index');
    }

    public function downloadStudentsTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_template.csv"',
        ];

        $csvData = "name,email,password\n";
        $csvData .= "John Doe,john.doe@example.com,password123\n";
        $csvData .= "Jane Smith,jane.smith@example.com,password456\n";

        return Response::make($csvData, 200, $headers);
    }

    public function downloadStudentsSample()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="students_sample.csv"',
        ];

        $csvData = "name,email,password\n";
        $csvData .= "Budi Santoso,budi.santoso@student.com,password\n";
        $csvData .= "Dewi Lestari,dewi.lestari@student.com,password\n";
        $csvData .= "Rizky Pratama,rizky.pratama@student.com,password\n";
        $csvData .= "Siti Nurhaliza,siti.nurhaliza@student.com,password\n";
        $csvData .= "Dimas Anggara,dimas.anggara@student.com,password\n";
        $csvData .= "Ratna Sari,ratna.sari@student.com,password\n";
        $csvData .= "Agus Setiawan,agus.setiawan@student.com,password\n";
        $csvData .= "Putri Handayani,putri.handayani@student.com,password\n";
        $csvData .= "Hendra Wijaya,hendra.wijaya@student.com,password\n";
        $csvData .= "Indah Permata,indah.permata@student.com,password\n";

        return Response::make($csvData, 200, $headers);
    }

    public function downloadCoursesTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="courses_template.csv"',
        ];

        $csvData = "name,code,description,lecturer_id,semester_id\n";
        $csvData .= "Introduction to Programming,CS101,Learn basic programming concepts,1,1\n";
        $csvData .= "Web Development Basics,CS102,HTML CSS JavaScript fundamentals,1,1\n";

        return Response::make($csvData, 200, $headers);
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $csvData = file_get_contents($file->getRealPath());
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            $header = array_shift($rows);

            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) continue; // Skip empty rows

                $data = array_combine($header, $row);
                
                if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields";
                    continue;
                }

                // Check if email already exists
                if (User::where('email', $data['email'])->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Email {$data['email']} already exists";
                    continue;
                }

                User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => 'student',
                    'email_verified_at' => now(),
                ]);

                $imported++;
            }

            $message = "Successfully imported {$imported} students.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->route('import.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('import.index')
                ->with('error', 'Error importing students: ' . $e->getMessage());
        }
    }

    public function importCourses(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $csvData = file_get_contents($file->getRealPath());
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            $header = array_shift($rows);

            $imported = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                if (empty(array_filter($row))) continue; // Skip empty rows

                $data = array_combine($header, $row);
                
                if (!isset($data['name']) || !isset($data['description'])) {
                    $errors[] = "Row " . ($index + 2) . ": Missing required fields (name, description)";
                    continue;
                }

                // Check if course name already exists
                if (Course::where('name', $data['name'])->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Course name '{$data['name']}' already exists";
                    continue;
                }

                // Check if course code already exists (if provided)
                if (isset($data['code']) && !empty($data['code']) && Course::where('code', $data['code'])->exists()) {
                    $errors[] = "Row " . ($index + 2) . ": Course code '{$data['code']}' already exists";
                    continue;
                }

                Course::create([
                    'name' => $data['name'],
                    'code' => $data['code'] ?? 'AUTO-' . time() . '-' . $imported,
                    'description' => $data['description'],
                    'lecturer_id' => (!empty($data['lecturer_id']) && is_numeric($data['lecturer_id'])) ? $data['lecturer_id'] : auth()->id(),
                    'semester_id' => (!empty($data['semester_id']) && is_numeric($data['semester_id'])) ? $data['semester_id'] : null,
                    'status' => 'active',
                ]);

                $imported++;
            }

            $message = "Successfully imported {$imported} courses.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->route('import.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('import.index')
                ->with('error', 'Error importing courses: ' . $e->getMessage());
        }
    }
}
