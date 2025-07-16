<?php

namespace App\Exports;

use App\Models\Assignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SubmissionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $assignment;

    public function __construct(Assignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Get the data collection for export
     */
    public function collection()
    {
        // Get all enrolled students for this course
        $enrolledStudents = $this->assignment->course->enrollments()->with('user')->get();
        
        return $enrolledStudents->map(function ($enrollment) {
            $student = $enrollment->user;
            $submission = $student->submissions()
                ->where('assignment_id', $this->assignment->id)
                ->with(['submissionScores.rubricCriteria'])
                ->first();
            
            $score = 0;
            $maxScore = $this->assignment->rubric ? $this->assignment->rubric->total_points : 100;
            $status = 'Not Submitted';
            $submissionDate = '-';
            $fileName = '-';
            $isLate = false;
            $feedback = '-';
            
            if ($submission) {
                $score = $submission->submissionScores->sum('score');
                $status = $submission->status === 'graded' ? 'Graded' : 'Submitted';
                $submissionDate = $submission->created_at->format('Y-m-d H:i');
                $fileName = $submission->original_filename ?? $submission->file_path;
                $isLate = $submission->created_at > $this->assignment->due_date;
                $feedback = $submission->feedback ?? '-';
            }
            
            $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;
            $grade = $this->calculateGrade($percentage);
            
            return (object) [
                'student_name' => $student->name,
                'student_email' => $student->email,
                'status' => $status,
                'submission_date' => $submissionDate,
                'file_name' => $fileName,
                'score' => $score,
                'max_score' => $maxScore,
                'percentage' => $percentage,
                'grade' => $grade,
                'is_late' => $isLate ? 'Yes' : 'No',
                'due_date' => $this->assignment->due_date ? $this->assignment->due_date->format('Y-m-d H:i') : 'Not set',
                'feedback' => $feedback
            ];
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
            'Status',
            'Submission Date',
            'File Name',
            'Score',
            'Max Score',
            'Percentage (%)',
            'Grade',
            'Late Submission',
            'Due Date',
            'Feedback'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($submission): array
    {
        return [
            $submission->student_name,
            $submission->student_email,
            $submission->status,
            $submission->submission_date,
            $submission->file_name,
            $submission->score,
            $submission->max_score,
            $submission->percentage,
            $submission->grade,
            $submission->is_late,
            $submission->due_date,
            $submission->feedback
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
                    'startColor' => ['argb' => 'e74c3c'],
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
            'C' => 12, // Status
            'D' => 18, // Submission Date
            'E' => 25, // File Name
            'F' => 10, // Score
            'G' => 12, // Max Score
            'H' => 12, // Percentage
            'I' => 8,  // Grade
            'J' => 15, // Late Submission
            'K' => 18, // Due Date
            'L' => 30, // Feedback
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
