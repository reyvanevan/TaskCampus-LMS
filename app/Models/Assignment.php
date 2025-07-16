<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Assignment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'deadline',
        'max_score',
        'file_path',
        'original_filename',
        'status',
        'allow_late_submissions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'deadline' => 'datetime',
        'allow_late_submissions' => 'boolean',
    ];

    /**
     * Get the course that owns the assignment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the submissions for the assignment.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get the rubric for the assignment.
     */
    public function rubric(): HasOne
    {
        return $this->hasOne(Rubric::class);
    }

    /**
     * Determine if the assignment is past due.
     */
    public function isPastDue(): bool
    {
        return now() > $this->deadline && !$this->allow_late_submissions;
    }
    
    /**
     * Determine if late submissions are allowed and the assignment is past due.
     */
    public function isLateSubmissionAllowed(): bool
    {
        return $this->allow_late_submissions && now() > $this->deadline;
    }
    
    /**
     * Determine if submissions are still accepted for this assignment
     */
    public function acceptsSubmissions(): bool
    {
        // Accept submissions if:
        // 1. Assignment is published AND
        // 2. Either before deadline OR late submissions allowed
        return $this->status === 'published' && 
               (now() <= $this->deadline || $this->allow_late_submissions);
    }
    
    /**
     * Get the time remaining until the deadline
     */
    public function timeRemaining(): string
    {
        if (now() > $this->deadline) {
            return 'Past due';
        }
        
        $diff = now()->diff($this->deadline);
        
        if ($diff->days > 0) {
            return $diff->days . ' days, ' . $diff->h . ' hours remaining';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours, ' . $diff->i . ' minutes remaining';
        } else {
            return $diff->i . ' minutes remaining';
        }
    }

    /**
     * Scope a query to only include published assignments.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}