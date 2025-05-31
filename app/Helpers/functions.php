<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('auth_check')) {
    /**
     * Check if the current user is authenticated
     *
     * @return bool
     */
    function auth_check()
    {
        return Auth::check();
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if the current user is an admin
     *
     * @return bool
     */
    function is_admin()
    {
        return Auth::check() && Auth::user()->isAdmin();
    }
}

if (!function_exists('current_user')) {
    /**
     * Get the currently authenticated user
     *
     * @return \App\Models\User|null
     */
    function current_user()
    {
        return Auth::user();
    }
}
