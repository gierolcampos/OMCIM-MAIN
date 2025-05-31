<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'event_type',
        'start_date_time',
        'end_date_time',
        'location',
        'location_details',
        'status',
        'notes',
        'image_path',
        'created_by',
        'school_calendar_id',
        'evaluation_open',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date_time' => 'datetime',
        'end_date_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'evaluation_open' => 'boolean',
    ];

    /**
     * Get the user who created the event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the school calendar that this event belongs to.
     */
    public function schoolCalendar(): BelongsTo
    {
        return $this->belongsTo(SchoolCalendar::class);
    }

    /**
     * Scope a query to only include events from the current academic year.
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
     * Determine if the event is upcoming.
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return $this->status === 'upcoming';
    }

    /**
     * Determine if the event is completed.
     *
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Determine if the event is cancelled.
     *
     * @return bool
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Determine if the event is pending (has passed but status not updated).
     *
     * @return bool
     */
    public function isPending(): bool
    {
        // An event is considered pending if it's still marked as upcoming
        // but the end date has passed
        if ($this->status !== 'upcoming') {
            return false;
        }

        $now = now();
        $endDate = $this->end_date_time;

        return $endDate < $now;
    }

    /**
     * Get the attendances for the event.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(EventAttendance::class);
    }

    /**
     * Get the attending users for the event.
     */
    public function attendingUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attendances')
            ->withPivot('status', 'comment')
            ->withTimestamps();
    }

    /**
     * Get the evaluations for the event.
     */
    public function evaluations(): HasMany
    {
        return $this->hasMany(EventEvaluation::class);
    }

    /**
     * Get the evaluation questions for the event.
     */
    public function evaluationQuestions(): HasMany
    {
        return $this->hasMany(EvaluationQuestion::class)->orderBy('display_order');
    }

    /**
     * Determine if the event evaluation is open.
     *
     * @return bool
     */
    public function isEvaluationOpen(): bool
    {
        return $this->evaluation_open === true;
    }
}
