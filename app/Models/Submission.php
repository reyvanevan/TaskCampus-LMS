<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'assignment_id',
        'file_path',
        'original_filename',
        'comment',
        'status',
        'score',
        'feedback',
        'is_late',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'score' => 'float',
        'is_late' => 'boolean',
    ];

    /**
     * Get the user that owns the submission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assignment that owns the submission.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the criteria scores for this submission.
     */
    public function criteriaScores(): HasMany
    {
        return $this->hasMany(SubmissionScore::class);
    }

    /**
     * Calculate the percentage score.
     */
    public function getPercentageScore(): float
    {
        if ($this->assignment && $this->assignment->max_score > 0 && $this->score !== null) {
            return ($this->score / $this->assignment->max_score) * 100;
        }

        return 0;
    }

    /**
     * Scope a query to only include submissions for a specific assignment.
     */
    public function scopeForAssignment($query, $assignmentId)
    {
        return $query->where('assignment_id', $assignmentId);
    }

    /**
     * Scope a query to only include submissions by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
    
    /**
     * Get the original filename from the file path
     */
    public function getOriginalFilename(): string
    {
        $filename = basename($this->file_path);
        // Strip timestamp if present
        $parts = explode('_', $filename);
        if (count($parts) > 1 && is_numeric($parts[count($parts) - 2])) {
            array_splice($parts, count($parts) - 2, 1);
            return implode('_', $parts);
        }
        return $filename;
    }
    
    /**
     * Get the public URL for the submission file
     */
    public function getFileUrl(): string
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : '#';
    }
    
    /**
     * Get the status badge HTML
     */
    public function getStatusBadge(): string
    {
        $colors = [
            'submitted' => 'bg-blue-100 text-blue-800',
            'late' => 'bg-yellow-100 text-yellow-800',
            'graded' => 'bg-green-100 text-green-800',
            'returned' => 'bg-purple-100 text-purple-800'
        ];
        
        $color = $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
        
        return '<span class="px-2 py-1 text-xs rounded-full ' . $color . '">' . 
                ucfirst($this->status) . '</span>';
    }
    
    /**
     * Get the rubric criteria scores for this submission.
     */
    public function scores()
    {
        return $this->hasMany(SubmissionScore::class);
    }
}