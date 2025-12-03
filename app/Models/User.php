<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',

        'studentnumber',
        'firstname',
        'lastname',
        'middlename',
        'suffix',
        'course',
        'major',
        'year',
        'section',
        'mobile_no',
        'email',
        'profile_picture',
        'password',
        'student_id',
        'course_year_section',
        'membership_type',
        'membership_expiry',
        'alternative_email',
        'department',
        'address',
        'notes',
        'status',
        'user_role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'studentnumber',
        'firstname',
        'lastname',
        'middlename',
        'suffix',
        'course',
        'major',
        'year',
        'section',
        'mobile_no',
        'email',
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deletion_requested_at' => 'datetime',
            'password' => 'hashed',
            'membership_expiry' => 'date',
            'status' => 'string',
            'user_role' => 'string',
        ];
    }

    /**
     * Get the role that the user belongs to.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the officer position that the user has.
     */
    public function officerPosition(): BelongsTo
    {
        return $this->belongsTo(OfficerPosition::class);
    }

    /**
     * Get the committees that the user belongs to.
     */
    public function committees(): BelongsToMany
    {
        return $this->belongsToMany(Committee::class, 'committee_members')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Get the committees that the user heads.
     */
    public function headedCommittees(): HasMany
    {
        return $this->hasMany(Committee::class, 'head_id');
    }

    /**
     * Check if the user is a superadmin.
     *
     * @return bool
     */
    public function isSuperadmin(): bool
    {
        $role = is_string($this->user_role) ? strtolower($this->user_role) : '';
        return in_array($role, ['super_admin', 'superadmin'], true);
    }

    

    /**
     * Check if the user is an administrator (for backward compatibility).
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        // Use the new role system
        return $this->isSuperadmin();
    }

    /**
     * Check if the user is active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the user can manage events.
     *
     * @return bool
     */
    public function canManageEvents(): bool
    {
        // Superadmins can manage everything
        if ($this->isSuperadmin()) {
            return true;
        }

        // Check for moderator role (case-insensitive)
        $role = is_string($this->user_role) ? strtolower($this->user_role) : '';
        if ($role === 'moderator') {
            return true;
        }

        // Secretary and PIO can manage events
        return in_array($this->user_role, ['Secretary', 'PIO']);
    }

    /**
     * Check if the user can manage announcements.
     *
     * @return bool
     */
    public function canManageAnnouncements(): bool
    {
        // Superadmins can manage everything
        if ($this->isSuperadmin()) {
            return true;
        }

        // Check for moderator role (case-insensitive)
        $role = is_string($this->user_role) ? strtolower($this->user_role) : '';
        if ($role === 'moderator') {
            return true;
        }

        // Secretary and PIO can manage announcements
        return in_array($this->user_role, ['Secretary', 'PIO']);
    }

    /**
     * Check if the user can manage payments.
     *
     * @return bool
     */
    public function canManagePayments(): bool
    {
        // Super admin and finance admin can manage payments (treated equally)
        $role = is_string($this->user_role) ? strtolower($this->user_role) : '';
        return in_array($role, ['super_admin', 'superadmin', 'finance_admin'], true);
    }

    /**
     * Determine if the user is a finance admin.
     */
    public function isFinanceAdmin(): bool
    {
        return is_string($this->user_role) && strtolower($this->user_role) === 'finance_admin';
    }

    /**
     * Determine if the user is any admin-level role.
     */
    public function isAdminRole(): bool
    {
        $role = is_string($this->user_role) ? strtolower($this->user_role) : '';
        return in_array($role, ['super_admin', 'superadmin', 'operations_admin', 'moderator', 'finance_admin'], true);
    }

    /**
     * Admins can view all payments, only finance admin can manage.
     */
    public function canViewAllPayments(): bool
    {
        return $this->isAdminRole();
    }

    /**
     * Check if the user can manage reports.
     *
     * @return bool
     */
    public function canManageReports(): bool
    {
        // Superadmins can manage everything
        if ($this->isSuperadmin()) {
            return true;
        }

        // Only Secretary can manage reports
        return $this->user_role === 'Secretary';
    }

    /**
     * Check if the user can manage members.
     *
     * @return bool
     */
    public function canManageMembers(): bool
    {
        // Superadmins can manage everything
        if ($this->isSuperadmin()) {
            return true;
        }

        // Only Secretary can manage members
        return $this->user_role === 'Secretary';
    }

    /**
     * Check if the user can generate reports.
     *
     * @return bool
     */
    public function canGenerateReports(): bool
    {
        // Superadmins can generate everything
        if ($this->isSuperadmin()) {
            return true;
        }

        // Only Secretary can generate reports
        return $this->user_role === 'Secretary';
    }

    /**
     * Get the user's full name.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return trim($this->firstname . ' ' . ($this->middlename ? $this->middlename . ' ' : '') . $this->lastname . ($this->suffix ? ' ' . $this->suffix : ''));
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the unread notifications count for the user.
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->notifications()->unread()->count();
    }

    /**
     * Check if the user has requested account deletion.
     *
     * @return bool
     */
    public function hasDeletionRequest(): bool
    {
        return $this->deletion_requested_at !== null;
    }
}
