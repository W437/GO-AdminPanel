<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Validates that the request includes a valid API key in the X-API-Key header.
     * This prevents casual access to public API endpoints.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-API-Key');
        $validApiKey = config('app.api_key');

        // Check if API key is provided
        if (empty($apiKey)) {
            return response()->json([
                'error' => 'API key required',
                'message' => 'Please provide a valid API key in the X-API-Key header'
            ], 401);
        }

        // Validate API key
        if ($apiKey !== $validApiKey) {
            return response()->json([
                'error' => 'Invalid API key',
                'message' => 'The provided API key is not valid'
            ], 401);
        }

        return $next($request);
    }
}
