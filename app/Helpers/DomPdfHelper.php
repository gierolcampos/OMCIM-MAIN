<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

/**
 * Helper functions for DomPDF
 */
class DomPdfHelper
{
    /**
     * Get the currently authenticated user
     *
     * @return \App\Models\User|null
     */
    public static function getAuthUser()
    {
        return Auth::user();
    }

    /**
     * Check if the current user is authenticated
     *
     * @return bool
     */
    public static function isAuthenticated()
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
        return Auth::check() && Auth::user()->isSuperadmin();
    }
}
