<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
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
    ];

    /**
     * Get the users that belong to this role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if this role is a superadmin role.
     */
    public function isSuperadmin(): bool
    {
        return $this->name === 'superadmin';
    }

    /**
     * Check if this role is an officer role.
     */
    public function isOfficer(): bool
    {
        return $this->name === 'officer' || $this->name === 'superadmin';
    }

    /**
     * Check if this role is a member role.
     */
    public function isMember(): bool
    {
        return $this->name === 'member';
    }

    /**
     * Check if this role is an admin role (for backward compatibility).
     */
    public function isAdmin(): bool
    {
        return $this->name === 'superadmin';
    }
}
