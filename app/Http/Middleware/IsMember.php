<?php

namespace App\Http\Middleware;

use Closure;

class IsMember
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
        $access = auth()->user()->access ?? null;
        if ($access == 'member' || $access == 'admin') {
            return $next($request);
        }

        return redirect('/')->with('error', "You don't have access.");
    }
}
