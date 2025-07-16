<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RubricCriteria extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'rubric_id',
        'title',
        'description',
        'max_score',
        'order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'max_score' => 'float',
        'order' => 'integer',
    ];

    /**
     * Get the rubric that owns the criteria.
     */
    public function rubric(): BelongsTo
    {
        return $this->belongsTo(Rubric::class);
    }

    /**
     * Get the submission scores for this criteria.
     */
    public function submissionScores(): HasMany
    {
        return $this->hasMany(SubmissionScore::class);
    }
}