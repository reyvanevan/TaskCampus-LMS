<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    //OTORISASI/AUTHORIZATION
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has the given role
        $method = 'is' . ucfirst($role);
        if (method_exists($request->user(), $method) && $request->user()->$method()) {
            return $next($request);
        }

        // Redirect if user doesn't have the role
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access this page.');
    }
}