<?php

namespace YektaDG\Medialibrary\Http\Middleware;

use Closure;

class CheckMediaAccess
{
    public function handle($request, Closure $next)
    {
        $condition = false;

        $request->push(['accessAllMedia' => $condition]);

        return $next($request);
    }
}