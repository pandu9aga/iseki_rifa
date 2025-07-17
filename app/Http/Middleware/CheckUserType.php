<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$types)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->type, $types)) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}
