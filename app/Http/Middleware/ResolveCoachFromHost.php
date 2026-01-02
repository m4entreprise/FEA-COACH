<?php

namespace App\Http\Middleware;

use App\Models\Coach;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveCoachFromHost
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extract subdomain from host
        $host = $request->getHost();
        $parts = explode('.', $host);
        
        // If we have a subdomain (more than 2 parts, e.g., slug.domain.com)
        if (count($parts) >= 3) {
            $slug = $parts[0];
            
            // Find the coach by slug or subdomain (don't filter by is_active yet)
            $coach = Coach::where('slug', $slug)
                ->orWhere('subdomain', $slug)
                ->with('user')
                ->firstOrFail();
            
            // Check if subscription is active
            $user = $coach->user;
            $isSubscriptionActive = $this->isSubscriptionActive($user);
            
            // If subscription is inactive, show unavailable page
            if (!$isSubscriptionActive) {
                return response()->view('coach-site.unavailable', [
                    'coach' => $coach,
                ], 200);
            }
            
            // Check if coach profile is active
            if (!$coach->is_active) {
                abort(404);
            }
            
            // Store the coach in the container for use throughout the request
            app()->instance(Coach::class, $coach);
            
            // Also share with views
            view()->share('coach', $coach);
        }
        
        return $next($request);
    }
    
    /**
     * Check if the user has an active subscription
     */
    private function isSubscriptionActive($user): bool
    {
        if (!$user) {
            return false;
        }
        
        // Check if on trial (and not cancelled)
        $isOnTrial = $user->trial_ends_at 
                     && now()->isBefore($user->trial_ends_at);
        
        if ($isOnTrial) {
            return true;
        }
        
        // Consider subscription active if status is 'active', 'on_trial', or 'active_promo'
        $activeStatuses = ['active', 'on_trial', 'active_promo'];
        
        return in_array($user->subscription_status, $activeStatuses, true);
    }
}
