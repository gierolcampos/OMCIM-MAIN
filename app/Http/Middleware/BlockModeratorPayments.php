<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockModeratorPayments
{
    /**
     * Handle an incoming request.
     * Blocks moderators from accessing payment routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user) {
            $role = is_string($user->user_role) ? strtolower($user->user_role) : '';
            if ($role === 'moderator') {
                abort(403, 'Moderators do not have access to the payment section.');
            }
        }
        
        return $next($request);
    }
}

