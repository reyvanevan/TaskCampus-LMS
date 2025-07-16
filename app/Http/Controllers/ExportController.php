<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class ExportController extends Controller
{
    /**
     * Export student grades for a specific course
     */
    public function exportCourseGrades(Request $request, Course $course)
    {
        // Check if user is lecturer of this course
        if ($course->lecturer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $format = $request->get('format', 'csv');
        // Create clean filename without random characters
        $courseName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $course->name);
        $filename = 'course_grades_' . $courseName . '_' . date('Y_m_d');

        try {
            // Get students and their submissions
            $students = $course->enrollments()->with([
                'user',
                'user.submissions' => function ($query) use ($course) {
                    $query->whereHas('assignment', function ($q) use ($course) {
                        $q->where('course_id', $course->id);
                    })->with('assignment');
                }
            ])->get();

            // Prepare data
            $data = [];
            $data[] = ['Student Name', 'Email', 'Assignment', 'Score', 'Max Score', 'Status', 'Submitted At'];

            foreach ($students as $enrollment) {
                $student = $enrollment->user;
                $submissions = $student->submissions;
                
                if ($submissions->count() > 0) {
                    foreach ($submissions as $submission) {
                        $data[] = [
                            $student->name,
                            $student->email,
                            $submission->assignment->title,
                            $submission->scores->sum('score'),
                            $submission->assignment->max_score ?? 100,
                            $submission->status ?? 'submitted',
                            $submission->created_at->format('Y-m-d H:i:s')
                        ];
                    }
                } else {
                    // Student with no submissions
                    $data[] = [
                        $student->name,
                        $student->email,
                        'No submissions',
                        0,
                        0,
                        'No submission',
                        ''
                    ];
                }
            }

            if ($format === 'excel') {
                // For now, use CSV format to avoid Excel compatibility issues
                // Until we upgrade to PHP 8.1+ with phpoffice/phpspreadsheet
                return $this->generateCsvResponse($data, $filename . '_excel_format');
            } else {
                return $this->generateCsvResponse($data, $filename);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export students enrolled in a course
     */
    public function exportCourseStudents(Request $request, Course $course)
    {
        // Check if user is lecturer of this course or admin
        if ($course->lecturer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $format = $request->get('format', 'csv');
        // Create clean filename without random characters
        $courseName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $course->name);
        $filename = 'course_students_' . $courseName . '_' . date('Y_m_d');

        try {
            // Get enrolled students
            $students = $course->enrollments()->with('user')->get();

            // Prepare data
            $data = [];
            $data[] = ['Student Name', 'Email', 'Enrolled At', 'Status'];

            foreach ($students as $enrollment) {
                $student = $enrollment->user;
                $data[] = [
                    $student->name,
                    $student->email,
                    $enrollment->created_at->format('Y-m-d H:i:s'),
                    'Active'
                ];
            }

            if ($format === 'excel') {
                // For now, use CSV format to avoid Excel compatibility issues
                return $this->generateCsvResponse($data, $filename . '_excel_format');
            } else {
                return $this->generateCsvResponse($data, $filename);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export assignment submissions
     */
    public function exportAssignmentSubmissions(Request $request, Assignment $assignment)
    {
        // Check if user is lecturer of this assignment's course or admin
        if ($assignment->course->lecturer_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized action.');
        }

        $format = $request->get('format', 'csv');
        // Create clean filename without random characters
        $assignmentTitle = preg_replace('/[^A-Za-z0-9\-_]/', '_', $assignment->title);
        $filename = 'assignment_submissions_' . $assignmentTitle . '_' . date('Y_m_d');

        try {
            // Get submissions with student info
            $submissions = $assignment->submissions()->with(['user', 'scores'])->get();

            // Prepare data
            $data = [];
            $data[] = ['Student Name', 'Email', 'Score', 'Max Score', 'Status', 'Submitted At', 'Graded At'];

            foreach ($submissions as $submission) {
                $totalScore = $submission->scores->sum('score');
                $maxScore = $assignment->max_score ?? 100;
                
                $data[] = [
                    $submission->user->name,
                    $submission->user->email,
                    $totalScore,
                    $maxScore,
                    $submission->status ?? 'submitted',
                    $submission->created_at->format('Y-m-d H:i:s'),
                    $submission->updated_at->format('Y-m-d H:i:s')
                ];
            }

            if ($format === 'excel') {
                return $this->generateExcelResponse($data, $filename, 'Assignment Submissions');
            } else {
                return $this->generateCsvResponse($data, $filename);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export student's personal grades
     */
    public function exportStudentGrades(Request $request)
    {
        $user = Auth::user();
        $format = $request->get('format', 'csv');
        // Create clean filename with student name
        $studentName = preg_replace('/[^A-Za-z0-9\-_]/', '_', $user->name);
        $filename = 'student_grades_' . $studentName . '_' . date('Y_m_d');

        try {
            // Get student's submissions across all courses
            $submissions = $user->submissions()->with(['assignment.course', 'scores'])->get();

            // Prepare data
            $data = [];
            $data[] = ['Course', 'Assignment', 'Score', 'Max Score', 'Percentage', 'Status', 'Submitted At'];

            foreach ($submissions as $submission) {
                $totalScore = $submission->scores->sum('score');
                $maxScore = $submission->assignment->max_score ?? 100;
                $percentage = $maxScore > 0 ? round(($totalScore / $maxScore) * 100, 2) : 0;
                
                $data[] = [
                    $submission->assignment->course->name,
                    $submission->assignment->title,
                    $totalScore,
                    $maxScore,
                    $percentage . '%',
                    $submission->status ?? 'submitted',
                    $submission->created_at->format('Y-m-d H:i:s')
                ];
            }

            if ($format === 'excel') {
                // For now, use CSV format to avoid Excel compatibility issues
                return $this->generateCsvResponse($data, $filename . '_excel_format');
            } else {
                return $this->generateCsvResponse($data, $filename);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate Excel response using simple HTML table that's more compatible
     */
    private function generateExcelResponse($data, $filename, $sheetName = 'Sheet1')
    {
        try {
            // Use simple HTML table format that Excel can handle better
            $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            $html .= '<head>';
            $html .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
            $html .= '<meta name="ProgId" content="Excel.Sheet">';
            $html .= '<meta name="Generator" content="Microsoft Excel 15">';
            $html .= '<style>';
            $html .= 'table { border-collapse: collapse; }';
            $html .= 'th { background-color: #f0f0f0; font-weight: bold; border: 1px solid #000; padding: 5px; }';
            $html .= 'td { border: 1px solid #000; padding: 5px; }';
            $html .= '</style>';
            $html .= '</head>';
            $html .= '<body>';
            $html .= '<table>';
            
            foreach ($data as $rowIndex => $row) {
                if ($rowIndex === 0) {
                    $html .= '<thead><tr>';
                    foreach ($row as $cell) {
                        $html .= '<th>' . htmlspecialchars($cell ?? '') . '</th>';
                    }
                    $html .= '</tr></thead><tbody>';
                } else {
                    $html .= '<tr>';
                    foreach ($row as $cell) {
                        $html .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
                    }
                    $html .= '</tr>';
                }
            }
            
            $html .= '</tbody></table>';
            $html .= '</body></html>';
            
            $fullFilename = $filename . '.xls';
            
            return response($html)
                ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
                ->header('Content-Disposition', 'attachment; filename="' . $fullFilename . '"')
                ->header('Cache-Control', 'no-cache, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
            
        } catch (\Exception $e) {
            \Log::error('Excel export error: ' . $e->getMessage());
            return back()->with('error', 'Excel export failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate CSV response
     */
    private function generateCsvResponse($data, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($data as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
