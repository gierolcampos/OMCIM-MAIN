<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolCalendar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'school_calendar_desc',
        'school_calendar_short_desc',
        'is_selected',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_selected' => 'boolean',
    ];

    /**
     * Get the announcements for this school calendar.
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Get the events for this school calendar.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the cash payments for this school calendar.
     */
    public function cashPayments()
    {
        return $this->hasMany(CashPayment::class);
    }

    /**
     * Get the gcash payments for this school calendar.
     */
    public function gcashPayments()
    {
        return $this->hasMany(GcashPayment::class);
    }

    /**
     * Get the non-ICS members for this school calendar.
     */
    public function nonIcsMembers()
    {
        return $this->hasMany(NonIcsMember::class);
    }

    /**
     * Get the currently selected school calendar.
     */
    public static function getCurrentCalendar()
    {
        return self::where('is_selected', true)->first();
    }

    /**
     * Get the ID of the currently selected school calendar.
     */
    public static function getCurrentCalendarId()
    {
        $calendar = self::getCurrentCalendar();
        return $calendar ? $calendar->id : null;
    }
}
