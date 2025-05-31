<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Committee extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'head_id',
        'school_calendar_id',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the head of the committee.
     */
    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_id');
    }

    /**
     * Get the school calendar that this committee belongs to.
     */
    public function schoolCalendar(): BelongsTo
    {
        return $this->belongsTo(SchoolCalendar::class);
    }

    /**
     * Get the members of the committee.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'committee_members')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active committees.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include committees from the current academic year.
     */
    public function scopeCurrentAcademicYear($query)
    {
        $currentCalendarId = SchoolCalendar::getCurrentCalendarId();
        return $query->where('school_calendar_id', $currentCalendarId);
    }
}
