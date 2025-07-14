<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControl
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
        if ($request->is('images/*')) {
            return response($next($request))
                ->header('Cache-Control', 'public, max-age=31536000')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        }

        return $next($request);
    }
}
