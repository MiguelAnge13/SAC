<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Si hay varios "guards", comprobar cada uno
        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        // Si ningún guard está autenticado, continuar con la petición
        return $next($request);
    }
}
