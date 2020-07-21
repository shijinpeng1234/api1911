<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redis;

class ViewCount
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
        $url = $request->getRequestUri();
        $u = substr($url,0,7);
        $date = date('Ymd');
        $z_count = "z_count";
        $key =$u .'_'.$z_count .'_'.$date;
        Redis::zincrby($key,1,"count");
        return $next($request);
    }
}
