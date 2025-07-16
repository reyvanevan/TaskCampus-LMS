<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
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
                    });
                }
            ])
            ->get()
            ->map(function ($enrollment) {
                $student = $enrollment->user;
                $totalAssignments = $this->course->assignments()->published()->count();
                $submittedAssignments = $student->submissions()
                    ->whereHas('assignment', function ($q) {
                        $q->where('course_id', $this->course->id);
                    })
                    ->count();
                
                $averageScore = $student->submissions()
                    ->whereHas('assignment', function ($q) {
                        $q->where('course_id', $this->course->id);
                    })
                    ->whereNotNull('final_score')
                    ->avg('final_score') ?? 0;

                return (object) [
                    'name' => $student->name,
                    'email' => $student->email,
                    'enrollment_date' => $enrollment->created_at->format('Y-m-d'),
                    'total_assignments' => $totalAssignments,
                    'submitted_assignments' => $submittedAssignments,
                    'completion_rate' => $totalAssignments > 0 ? round(($submittedAssignments / $totalAssignments) * 100, 2) : 0,
                    'average_score' => round($averageScore, 2),
                    'status' => $enrollment->status ?? 'active'
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
            'Enrollment Date',
            'Total Assignments',
            'Submitted',
            'Completion Rate (%)',
            'Average Score',
            'Status'
        ];
    }

    /**
     * Map data for each row
     */
    public function map($student): array
    {
        return [
            $student->name,
            $student->email,
            $student->enrollment_date,
            $student->total_assignments,
            $student->submitted_assignments,
            $student->completion_rate,
            $student->average_score,
            ucfirst($student->status)
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
                    'startColor' => ['argb' => '16a085'],
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
            'A' => 25, // Student Name
            'B' => 30, // Email
            'C' => 18, // Enrollment Date
            'D' => 18, // Total Assignments
            'E' => 12, // Submitted
            'F' => 18, // Completion Rate
            'G' => 15, // Average Score
            'H' => 12, // Status
        ];
    }
}
