<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class checkLanguage
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
        //app().getLocale()
        app()->setLocale('en');
        if($request->header('Accept-Language') =="ar")
            app()->setLocale('ar');
        return $next($request);
    }
}
