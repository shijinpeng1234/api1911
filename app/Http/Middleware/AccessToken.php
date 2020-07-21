<?php

namespace App\Http\Middleware;

use App\Models\P_users;
use Closure;
use Illuminate\Support\Facades\Redis;

class AccessToken
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
        $token =$request->get('token');
        if(!$token){
            $data = [
                'errno'=>50001,
                'msg'  =>"未授权"
            ];
            echo json_encode($data);die;
        }else{
            $red_token = Redis::get('token');
            if($token!=$red_token){
                $data = [
                    'errno' => 50005,
                    'msg'   => "授权失败"
                ];
                echo json_encode($data);die;
            }
        }
        return $next($request);
    }
}