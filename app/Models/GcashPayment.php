<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GcashPayment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'school_calendar_id',
        'email',
        'total_price',
        'purpose',
        'placed_on',
        'payment_status',
        'gcash_name',
        'gcash_num',
        'reference_number',
        'gcash_proof_path',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'placed_on' => 'datetime',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user that made the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the school calendar that this payment belongs to.
     */
    public function schoolCalendar()
    {
        return $this->belongsTo(SchoolCalendar::class);
    }

    /**
     * Scope a query to only include payments from the current academic year.
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
     * Get masked name for GCASH receipts.
     */
    public function getMaskedNameAttribute()
    {
        $gcashName = $this->gcash_name;

        if (!$gcashName) {
            return '';
        }

        $maskedName = '';
        $nameParts = explode(' ', $gcashName);

        foreach ($nameParts as $part) {
            if (strlen($part) > 2) {
                $maskedPart = substr($part, 0, 1) . str_repeat('*', strlen($part) - 2) . substr($part, -1);
            } else {
                $maskedPart = $part;
            }
            $maskedName .= $maskedPart . ' ';
        }

        return trim($maskedName);
    }
}
