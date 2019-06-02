<?php

namespace cbp\Http\Middleware;

use Closure;

class Admin
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
        
            if(auth()->user()->is_admin == 1)
            {
                return $next($request);
            }
            return redirect('no_access');

    }
}
