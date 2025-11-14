<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSetupCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si pas d'utilisateur, laisser passer
        if (!$user) {
            return $next($request);
        }

        // Les admins n'ont pas besoin du setup
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Si le setup n'est pas complété et qu'on n'est pas déjà sur une route setup
        if ($user->onboarding_completed && !$user->setup_completed && !$request->routeIs('setup.*')) {
            return redirect()->route('setup.index');
        }

        return $next($request);
    }
}
