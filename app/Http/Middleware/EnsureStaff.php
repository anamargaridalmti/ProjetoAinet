<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Allows only Employees (F) and Admins (A) to pass.
 * Customers and anonymous users are rejected with 403.
 */
class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $userType = auth()->user()?->user_type;

        if (! in_array($userType, ['F', 'A'], true)) {
            abort(403, 'Acesso restrito a funcionários e administradores.');
        }

        return $next($request);
    }
}
