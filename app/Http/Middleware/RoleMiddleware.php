<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        if (!$request->user()->hasRole($role)) {
            abort(403, 'Unauthorized Access. You do not have the right roles.');
        }

        return $next($request);
    }
}
