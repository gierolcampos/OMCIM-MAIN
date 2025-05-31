<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  The permission name (manage-events, manage-announcements, manage-payments)
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Check if the user has the required permission
        switch ($permission) {
            case 'manage-events':
                if (!$user->canManageEvents()) {
                    abort(403, 'Unauthorized. You do not have permission to manage events.');
                }
                break;
            case 'manage-announcements':
                if (!$user->canManageAnnouncements()) {
                    abort(403, 'Unauthorized. You do not have permission to manage announcements.');
                }
                break;
            case 'manage-payments':
                if (!$user->canManagePayments()) {
                    abort(403, 'Unauthorized. You do not have permission to manage payments.');
                }
                break;
            case 'manage-reports':
                if (!$user->canManageReports()) {
                    abort(403, 'Unauthorized. You do not have permission to manage reports.');
                }
                break;
            case 'manage-members':
                if (!$user->canManageMembers()) {
                    abort(403, 'Unauthorized. You do not have permission to manage members.');
                }
                break;
            default:
                // If the permission is not recognized, deny access
                abort(403, 'Unauthorized. Unknown permission requested.');
        }

        return $next($request);
    }
}
