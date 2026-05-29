<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOnboardingCompleted
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !Auth::user()->has_completed_onboarding) {
            $user = Auth::user();
            if ($user->teams()->exists()) {
                $user->has_completed_onboarding = true;
                $user->save();
                return $next($request);
            }

            // Avoid infinite redirect loop
            if (!$request->is('onboarding*') && !$request->is('logout') && !$request->expectsJson() && !$request->is('api/*')) {
                return redirect()->route('onboarding.show');
            }
        }

        return $next($request);
    }
}
