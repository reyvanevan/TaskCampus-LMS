<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rubric extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'assignment_id',
        'name',
        'description',
    ];

    /**
     * Get the assignment that owns the rubric.
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the criteria for the rubric.
     */
    public function criteria(): HasMany
    {
        return $this->hasMany(RubricCriteria::class);
    }

    /**
     * Calculate the total possible score for the rubric.
     */
    public function getTotalPossibleScore(): float
    {
        return $this->criteria()->sum('max_score');
    }
}