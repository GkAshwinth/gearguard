<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOwnerHasTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is an owner, but they haven't set up 2FA yet...
        if ($request->user() && $request->user()->isOwner() && !$request->user()->two_factor_secret) {
            // Force them to their profile page to set it up!
            return redirect()->route('profile.show')
                ->with('status', 'SECURITY ALERT: As an Owner, you must enable Two-Factor Authentication before accessing the dashboard.');
        }

        return $next($request);
    }
}
