<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        if (! $request->user()->isStaff()) {
            if ($request->expectsJson() || $request->ajax()) {
                abort(403, 'Unauthorized access.');
            }

            return redirect()->route('user.dashboard')->with('error', 'You do not have administrative access. Redirected to your dashboard.');
        }

        return $next($request);
    }
}
