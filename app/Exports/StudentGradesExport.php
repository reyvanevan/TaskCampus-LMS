<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentGradesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $student;

    public function __construct(User $student)
    {
        $this->student = $student;
    }

    /**
     * Get the data collection for export
     */
    public function collection()
    {
        return $this->student->submissions()
            ->with([
                'assignment.course',
                'assignment.rubric',
                'submissionScores.rubricCriteria'
            ])
            ->get()
            ->map(function ($submission) {
                $assignment = $submission->assignment;
                $course = $assignment->course;
                
                $score = $submission->submissionScores->sum('score');
                $maxScore = $assignment->rubric ? $assignment->rubric->total_points : 100;
                $percentage = $maxScore > 0 ? round(($score / $maxScore) * 100, 2) : 0;
                $grade = $this->calculateGrade($percentage);
                $isLate = $submission->created_at > $assignment->due_date;
                
                return (object) [
                    'course_name' => $course->name,
                    'assignment_title' => $assignment->title,
                    'due_date' => $assignment->due_date ? $assignment->due_date->format('Y-m-d H:i') : 'Not set',
                    'submission_date' => $submission->created_at->format('Y-m-d H:i'),
                    'score' => $score,
                    'max_score' => $maxScore,
                    'percentage' => $percentage,
                    'grade' => $grade,
                    'status' => $submission->status === 'graded' ? 'Graded' : 'Submitted',
                    'is_late' => $isLate ? 'Yes' : 'No',
                    'feedback' => $submission->feedback ?? '-'
                ];
            })
            ->sortBy(['course_name', 'assignment_title']);
    }

    /**
     * Set the headings for the export
     */
    public function headings(): array
    {
        return [
            'Course',
            'Assignment',
            'Due Date',
            'Submission Date',
            'Score',
            'Max Score',
            'Percentage (%)',
            'Grade',
            'Status',
            'Late Submission',
            'Feedback'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($submission): array
    {
        return [
            $submission->course_name,
            $submission->assignment_title,
            $submission->due_date,
            $submission->submission_date,
            $submission->score,
            $submission->max_score,
            $submission->percentage,
            $submission->grade,
            $submission->status,
            $submission->is_late,
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
                    'startColor' => ['argb' => '9b59b6'],
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
            'A' => 25, // Course
            'B' => 30, // Assignment
            'C' => 18, // Due Date
            'D' => 18, // Submission Date
            'E' => 10, // Score
            'F' => 12, // Max Score
            'G' => 12, // Percentage
            'H' => 8,  // Grade
            'I' => 12, // Status
            'J' => 15, // Late Submission
            'K' => 35, // Feedback
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
