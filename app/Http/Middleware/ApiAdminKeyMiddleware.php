<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiAdminKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $providedKey = $request->header('X-ADMIN-KEY') ?: $request->query('admin_key');
        $validKey = env('MICRO_ADMIN_KEY');

        if (!$validKey || $providedKey !== $validKey) {
            return response()->json(['error' => 'Unauthorized: admin key required'], 401);
        }

        return $next($request);
    }
}
