<?php

namespace App\Http\Middleware;

use App\Models\SecurityLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckRole Middleware
 *
 * SECURITY: Enforces Role-Based Access Control (RBAC).
 * Prevents URL-hacking attacks where a client navigates to /owner/dashboard.
 *
 * BONUS: Every failed attempt is logged to security_logs table
 * — this matches friend's SecurityLog model usage.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user() || $request->user()->role !== $role) {

            // Log the IDOR attempt to security_logs
            try {
                SecurityLog::create([
                    'action'      => 'idor_attempt',
                    'description' => "Unauthorised access attempt to {$request->path()} — required role: {$role}",
                    'user_id'     => $request->user()?->id,
                    'ip_address'  => $request->ip(),
                    'user_agent'  => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {
                // Don't crash if logging fails
            }

            abort(403, 'Access denied. You do not have permission to view this page.');
        }

        return $next($request);
    }
}
