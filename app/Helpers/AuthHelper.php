<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

/**
 * Auth helper functions
 */
class AuthHelper
{
    /**
     * Check if the current user is authenticated
     *
     * @return bool
     */
    public static function check()
    {
        return Auth::check();
    }

    /**
     * Check if the current user is an admin
     *
     * @return bool
     */
    public static function isAdmin()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Get the currently authenticated user
     *
     * @return \App\Models\User|null
     */
    public static function user()
    {
        return Auth::user();
    }
}
