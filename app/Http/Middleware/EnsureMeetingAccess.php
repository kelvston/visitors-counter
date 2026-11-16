<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMeetingAccess
{
    public function handle(Request $request, Closure $next, string $section, string $access = 'read'): Response
    {
        $user = $request->user();

        $allowed = $user && (
            $user->isAdmin()
            || ($access === 'manage'
                ? $user->canManageMinutesFor($section)
                : $user->staff_category === $section || $user->canManageMinutesFor($section))
        );

        abort_unless($allowed, 403);

        return $next($request);
    }
}
