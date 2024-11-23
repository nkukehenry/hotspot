<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ExcludeCors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the request is for the specific route
        if ($request->is('jpesa/callback')) {
            // If it is, get the response
            $response = $next($request);
            
            // Allow all origins
            $response->headers->set('Access-Control-Allow-Origin', '*');
           
            return $response;
        }

        // For other routes, just proceed without modifying CORS headers
        return $next($request);
    }
}