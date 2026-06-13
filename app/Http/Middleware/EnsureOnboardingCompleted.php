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
        if (Auth::check()) {
            $user = Auth::user();

            if (!$user->has_completed_onboarding) {
                if ($user->teams()->exists()) {
                    $user->has_completed_onboarding = true;
                    $user->save();
                    // Fallthrough to specialty check below
                } else {
                    // Avoid infinite redirect loop
                    if (!$request->is('onboarding*') && !$request->is('logout') && !$request->expectsJson() && !$request->is('api/*')) {
                        return redirect()->route('onboarding.show');
                    }
                    return $next($request);
                }
            }

            // Force specialty check for users who completed onboarding (or legacy users)
            if (empty($user->specialty)) {
                if (!$request->is('profile*') && !$request->is('logout') && !$request->expectsJson() && !$request->is('api/*')) {
                    session()->flash('error', 'Please add your Client Field / Specialty to your profile to continue using the app.');
                    return redirect()->route('profile.edit');
                }
            }
        }

        return $next($request);
    }
}
