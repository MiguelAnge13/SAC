<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $providedKey = $request->header('X-API-KEY') ?: $request->query('api_key');
        $validKey = env('MICRO_API_KEY');

        if (!$validKey || $providedKey !== $validKey) {
            return response()->json(['error' => 'Unauthorized: invalid or missing API key'], 401);
        }

        return $next($request);
    }
}
