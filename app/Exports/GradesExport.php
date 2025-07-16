<?php

namespace App\Exports;

use App\Models\Course;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class GradesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    /**
     * Get the data collection for export
     */
    public function collection()
    {
        return $this->course->enrollments()
            ->with([
                'user',
                'user.submissions' => function ($query) {
                    $query->whereHas('assignment', function ($q) {
                        $q->where('course_id', $this->course->id);
                    })->with(['assignment', 'submissionScores.rubricCriteria']);
                }
            ])
            ->get()
            ->flatMap(function ($enrollment) {
                $student = $enrollment->user;
                $assignments = $this->course->assignments()->published()->get();
                
                $rows = [];
                foreach ($assignments as $assignment) {
                    $submission = $student->submissions()
                        ->where('assignment_id', $assignment->id)
                        ->first();
                    
                    $score = 0;
                    $maxScore = $assignment->rubric ? $assignment->rubric->total_points : 100;
                    $status = 'Not Submitted';
                    $submissionDate = '-';
                    $isLate = false;
                    
                    if ($submission) {
                        $score = $submission->submissionScores->sum('score');
                        $status = $submission->status === 'graded' ? 'Graded' : 'Submitted';
                        $submissionDate = $submission->created_at->format('Y-m-d H:i');
                        $isLate = $submission->created_at > $assignment->due_date;
                    }
                    
                    $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;
                    $grade = $this->calculateGrade($percentage);
                    
                    $rows[] = (object) [
                        'student_name' => $student->name,
                        'student_email' => $student->email,
                        'assignment_title' => $assignment->title,
                        'score' => $score,
                        'max_score' => $maxScore,
                        'percentage' => $percentage,
                        'grade' => $grade,
                        'status' => $status,
                        'submission_date' => $submissionDate,
                        'is_late' => $isLate ? 'Yes' : 'No',
                        'due_date' => $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : 'Not set'
                    ];
                }
                
                return $rows;
            });
    }

    /**
     * Set the headings for the export
     */
    public function headings(): array
    {
        return [
            'Student Name',
            'Email',
            'Assignment',
            'Score',
            'Max Score',
            'Percentage (%)',
            'Grade',
            'Status',
            'Submission Date',
            'Late Submission',
            'Due Date'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($row): array
    {
        return [
            $row->student_name,
            $row->student_email,
            $row->assignment_title,
            $row->score,
            $row->max_score,
            $row->percentage,
            $row->grade,
            $row->status,
            $row->submission_date,
            $row->is_late,
            $row->due_date
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headers)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => '366092'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20, // Student Name
            'B' => 25, // Email
            'C' => 30, // Assignment
            'D' => 10, // Score
            'E' => 12, // Max Score
            'F' => 12, // Percentage
            'G' => 8,  // Grade
            'H' => 12, // Status
            'I' => 18, // Submission Date
            'J' => 15, // Late Submission
            'K' => 18, // Due Date
        ];
    }

    /**
     * Calculate letter grade from percentage
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 85) return 'A-';
        if ($percentage >= 80) return 'B+';
        if ($percentage >= 75) return 'B';
        if ($percentage >= 70) return 'B-';
        if ($percentage >= 65) return 'C+';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 55) return 'C-';
        if ($percentage >= 50) return 'D';
        return 'F';
    }
}
