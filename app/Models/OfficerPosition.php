<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficerPosition extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'can_manage_events',
        'can_manage_announcements',
        'can_manage_payments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'can_manage_events' => 'boolean',
        'can_manage_announcements' => 'boolean',
        'can_manage_payments' => 'boolean',
    ];

    /**
     * Get the users that have this officer position.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this position can manage events.
     */
    public function canManageEvents(): bool
    {
        return $this->can_manage_events;
    }

    /**
     * Check if this position can manage announcements.
     */
    public function canManageAnnouncements(): bool
    {
        return $this->can_manage_announcements;
    }

    /**
     * Check if this position can manage payments.
     */
    public function canManagePayments(): bool
    {
        return $this->can_manage_payments;
    }
}
