<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  The role name (superadmin, admin, member)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if the user has the required role
        if ($role === 'superadmin' && !$user->isSuperadmin()) {
            abort(403, 'Unauthorized. Superadmin access required.');
        }

        if ($role === 'officer' && !$user->isOfficer()) {
            abort(403, 'Unauthorized. Officer access required.');
        }

        if ($role === 'admin' && !$user->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // For 'member' role, all authenticated users can access
        // No need to check specifically for member role

        return $next($request);
    }
}
