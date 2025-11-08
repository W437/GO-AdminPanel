<?php

namespace App\Http\Middleware;

use Closure;

class InstallationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Purchase code check removed - no longer needed for custom deployment
        // Installation is controlled by APP_INSTALL environment variable
        return $next($request);
    }
}
