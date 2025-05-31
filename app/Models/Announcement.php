<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class Announcement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'status',
        'priority',
        'publish_date',
        'expiry_date',
        'created_by',
        'school_calendar_id',
        'image_path',
        'media_path',
        'text_color',
        'is_boosted',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'publish_date' => 'datetime',
        'expiry_date' => 'datetime',
    ];

    /**
     * Get the user who created the announcement.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the school calendar that this announcement belongs to.
     */
    public function schoolCalendar()
    {
        return $this->belongsTo(SchoolCalendar::class);
    }

    /**
     * Scope a query to only include published announcements.
     */
    public function scopePublished($query)
    {
        Log::info('Applying published scope');
        $now = Carbon::now();
        Log::info('Current time: ' . $now->toDateTimeString());

        // Debug all announcements before filtering
        $allAnnouncements = Announcement::all();
        Log::info('Total announcements in database: ' . $allAnnouncements->count());
        foreach ($allAnnouncements as $announcement) {
            Log::info('Checking announcement: ID: ' . $announcement->id .
                      ', Status: ' . $announcement->status .
                      ', Publish date: ' . ($announcement->publish_date ? $announcement->publish_date->toDateTimeString() : 'NULL') .
                      ', Expiry date: ' . ($announcement->expiry_date ? $announcement->expiry_date->toDateTimeString() : 'NULL'));
        }

        return $query->where('status', 'published')
            ->where(function($q) use ($now) {
                $q->whereNull('publish_date')
                  ->orWhere('publish_date', '<=', $now->toDateTimeString());
            })
            ->where(function($q) use ($now) {
                $q->whereNull('expiry_date')
                  ->orWhere('expiry_date', '>=', $now->toDateTimeString());
            });
    }

    /**
     * Check if announcement is active (published and not expired).
     */
    public function isActive()
    {
        if ($this->status !== 'published') {
            return false;
        }

        $now = Carbon::now();

        if ($this->publish_date && $this->publish_date->gt($now)) {
            return false;
        }

        if ($this->expiry_date && $this->expiry_date->lt($now)) {
            return false;
        }

        return true;
    }

    /**
     * Scope a query to only include announcements from the current academic year.
     */
    public function scopeCurrentAcademicYear($query)
    {
        $currentCalendarId = SchoolCalendar::getCurrentCalendarId();

        if ($currentCalendarId) {
            return $query->where('school_calendar_id', $currentCalendarId);
        }

        return $query;
    }

    /**
     * Get the human-readable time difference.
     */
    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
