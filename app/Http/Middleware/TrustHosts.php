<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array
     */
    public function hosts()
    {
        $hosts = [
            // Get domains from environment variables
            env('ADMIN_DOMAIN', 'hq-secure-panel-1337.hopa.delivery'),
            env('API_DOMAIN', 'api.hopa.delivery'),

            // Old subdomain for backward compatibility (can be removed later)
            'admin.hopa.delivery',

            // Main domain
            'hopa.delivery',
            'www.hopa.delivery',

            // Development & direct access
            'localhost',
            '127.0.0.1',

            // All subdomains of the application URL
            $this->allSubdomainsOfApplicationUrl(),
        ];

        // Add server IP if it exists in environment
        if (env('SERVER_IP')) {
            $hosts[] = env('SERVER_IP');
        }

        return array_filter($hosts); // Remove any null values
    }
}