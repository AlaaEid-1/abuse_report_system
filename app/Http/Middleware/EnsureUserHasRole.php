<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $userRole = $request->user()->role instanceof UserRole
            ? $request->user()->role->value
            : (string) $request->user()->role;

        // Super Admin bypasses role checks
        if ($userRole === UserRole::SUPER_ADMIN->value) {
            return $next($request);
        }

        if (empty($roles)) {
            return $next($request);
        }

        if (in_array($userRole, $roles, true)) {
            return $next($request);
        }

        abort(403, 'Unauthorized access to compliance portal.');
    }
}
