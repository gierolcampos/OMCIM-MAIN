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
        'reference_id',
        'title',
        'message',
        'is_read',
        'read_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that the notification belongs to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Mark the notification as read.
     */
    public function markAsRead()
    {
        $this->is_read = true;
        $this->read_at = now();
        $this->save();
    }

    /**
     * Get the reference URL based on notification type.
     */
    public function getReferenceUrl()
    {
        switch ($this->type) {
            case 'event':
                return route('events.show', $this->reference_id);
            case 'announcement':
                return route('announcements.show', $this->reference_id);
            case 'evaluation':
                return route('events.evaluation', $this->reference_id);
            default:
                return '#';
        }
    }

    /**
     * Get the icon class based on notification type.
     */
    public function getIconClass()
    {
        switch ($this->type) {
            case 'event':
                return 'fa-calendar-alt text-blue-500';
            case 'announcement':
                return 'fa-bullhorn text-yellow-500';
            case 'evaluation':
                return 'fa-clipboard-check text-green-500';
            default:
                return 'fa-bell text-gray-500';
        }
    }
}
