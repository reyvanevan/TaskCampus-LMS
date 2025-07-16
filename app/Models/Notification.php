<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
        'scheduled_for',
        'sent_email',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'sent_email' => 'boolean',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    /**
     * Check if the notification is unread.
     */
    public function isUnread(): bool
    {
        return is_null($this->read_at);
    }

    /**
     * Scope for unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for notifications of a specific type.
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get the icon for the notification type.
     */
    public function getIcon(): string
    {
        return match($this->type) {
            'assignment_created' => '📝',
            'assignment_graded' => '✅',
            'deadline_reminder' => '⏰',
            'course_enrolled' => '🎓',
            'submission_received' => '📄',
            'grade_updated' => '📊',
            'course_created' => '🆕',
            'user_created' => '👤',
            default => '🔔',
        };
    }

    /**
     * Get the color class for the notification type.
     */
    public function getColorClass(): string
    {
        return match($this->type) {
            'assignment_created' => 'bg-blue-100 text-blue-600',
            'assignment_graded' => 'bg-green-100 text-green-600',
            'deadline_reminder' => 'bg-yellow-100 text-yellow-600',
            'course_enrolled' => 'bg-purple-100 text-purple-600',
            'submission_received' => 'bg-indigo-100 text-indigo-600',
            'grade_updated' => 'bg-pink-100 text-pink-600',
            'course_created' => 'bg-emerald-100 text-emerald-600',
            'user_created' => 'bg-orange-100 text-orange-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Get the priority level of the notification.
     */
    public function getPriority(): string
    {
        return match($this->type) {
            'deadline_reminder' => 'high',
            'assignment_graded' => 'high',
            'assignment_created' => 'medium',
            'course_enrolled' => 'medium',
            'submission_received' => 'medium',
            'grade_updated' => 'medium',
            default => 'low',
        };
    }

    /**
     * Get the notification category.
     */
    public function getCategory(): string
    {
        return match($this->type) {
            'assignment_created', 'assignment_graded', 'deadline_reminder' => 'assignment',
            'course_enrolled', 'course_created' => 'course',
            'submission_received' => 'submission',
            'grade_updated' => 'grade',
            'user_created' => 'system',
            default => 'general',
        };
    }
}
