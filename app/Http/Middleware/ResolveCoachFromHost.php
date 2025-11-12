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
            
            // Find the coach by slug or subdomain
            $coach = Coach::where('slug', $slug)
                ->orWhere('subdomain', $slug)
                ->where('is_active', true)
                ->firstOrFail();
            
            // Store the coach in the container for use throughout the request
            app()->instance(Coach::class, $coach);
            
            // Also share with views
            view()->share('coach', $coach);
        }
        
        return $next($request);
    }
}
