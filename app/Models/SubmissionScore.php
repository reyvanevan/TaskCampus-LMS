<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionScore extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'submission_id',
        'rubric_criteria_id',
        'score',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'score' => 'float',
    ];

    /**
     * Get the submission that owns the score.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * Get the rubric criteria that owns the score.
     */
    public function rubricCriteria(): BelongsTo
    {
        return $this->belongsTo(RubricCriteria::class);
    }

    /**
     * Calculate the percentage score.
     */
    public function getPercentageScore(): float
    {
        if ($this->rubricCriteria && $this->rubricCriteria->max_score > 0) {
            return ($this->score / $this->rubricCriteria->max_score) * 100;
        }

        return 0;
    }
}