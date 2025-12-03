<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
	/**
	 * Handle an incoming request.
	 * Usage: role:super_admin,finance_admin
	 */
	public function handle(Request $request, Closure $next, ...$roles): Response
	{
		$user = $request->user();
		if (!$user) {
			abort(403, 'Unauthorized.');
		}

		// Super admin bypass (supports legacy 'superadmin')
		$roleValue = is_string($user->user_role) ? strtolower($user->user_role) : '';
		if (in_array($roleValue, ['super_admin', 'superadmin'], true)) {
			return $next($request);
		}

		// Finance admin bypass for payment routes (treated like super_admin in payment section)
		if ($roleValue === 'finance_admin' && ($request->is('admin/payments*') || $request->is('admin/payment-types*'))) {
			return $next($request);
		}

		$allowed = array_map(fn($r) => strtolower(trim((string)$r)), $roles);
		if (in_array($roleValue, $allowed, true)) {
			return $next($request);
		}

		abort(403, 'Forbidden.');
	}
}