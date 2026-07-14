<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect('login');
        }

        if (!$request->user()->hasPermission($permission)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk fitur ini.');
        }

        return $next($request);
    }
}
