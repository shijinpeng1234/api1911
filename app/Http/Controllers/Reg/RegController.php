<?php

namespace App\Http\Controllers\Reg;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use App\Models\P_users;
use App\Models\TokenModel;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector;

class RegController extends Controller
{
    /**
     * @param Request $request
     * @return array
     * 注册
     */
    public function reg(Request $request)
    {
        $user_name = $request->post('user_name');
        $password = $request->post('password');
        $email = $request->post('email');
        $reg_time = time();
        if(empty($user_name)){
            $data = [
                'errno' => '50001',
                'msg'   => "用户名不能为空"
            ];
            return $data;
        }
        if(empty($password)){
            $data = [
                'errno' => '50002',
                'msg'   => "密码不能为空"
            ];
            return $data;
        }
        if(empty($email)){
            $data = [
                'errno'  =>  '50003',
                'msg'    =>  "Email不能为空"
            ];
            return $data;
        }
        $p_usersModel = new P_users();
        $p_usersModel ->user_name =$user_name;
        $p_usersModel ->password  = password_hash($password,PASSWORD_BCRYPT);
        $p_usersModel ->email =$email;
        $p_usersModel ->reg_time=$reg_time;
        $add =$p_usersModel->save();
        if($add){
            $data = [
                'errno'  =>  '0',
                'msg'    =>  "添加成功"
            ];
            return $data;
        }else{
            $data = [
                'errno'  =>  '50004',
                'msg'    =>  "添加失败"
            ];
            return $data;
        }
    }
    /**
     * @param Request $request
     * @return array
     * 登录
     */
    public function login(Request $request)
    {
        $user_name = $request->post('user_name');
        $password = $request->post('password');
        if(empty($user_name)){
            $data = [
                'errno' => '50001',
                'msg'   => "用户名不能为空"
            ];
            return $data;
        }
        if(empty($password)){
            $data = [
                'errno' => '50002',
                'msg'   => "密码不能为空"
            ];
            return $data;
        }
        $p_usersModel = new P_users();
        $u = $p_usersModel::where(['user_name'=>$user_name])->first();
        if(!$u){
            $data = [
                'errno' => '50006',
                'msg'   => "用户名不存在"
            ];
            return $data;
        }
        if(password_verify($password,$u->password)){
            //生成token
            $token = Str::random(32);

            $expire_seconds = 7200; //token的有效期
//            //入库
//            $arr = [
//                'token'     => $token,
//                'uid'       => $u->user_id,
//                'expire_at' =>time() + $expire_seconds
//            ];
//            TokenModel::insertGetid($arr);
            Redis::set('token',$token);
            Redis::set('user_id',$u->user_id);
            Redis::expire('token',7200);
            Redis::expire('user_id',7200);
            $data = [
                'errno' => '0',
                'msg'   => "登录成功",
                'data'  => [
                    'token'     =>$token,
                    'expire_in' =>$expire_seconds
                ]
            ];
        }else{
            $data = [
                'errno' => '50008',
                'msg'   => "密码错误"
            ];
        }
        return $data;
    }

    /**
     * @param Request $request
     * @return array
     * 个人中心
     */
    public function center(Request $request)
    {
//        验证token是否存在
        $token = $request ->get('token');
        if(empty($token)){
            $data = [
                'errno'  => 400003,
                'msg'    => '未授权'
            ];
            return $data;
        }
        //验证token是否有效
//        $t = TokenModel::where(['token'=>$token])->first();
        $redis_token = Redis::get('token');
        if($token==$redis_token){
            $blacktoken = Redis::sismember('blacktoken',$token);
            if($blacktoken){
                $data = [
                    'errno'=> 50009,
                    'msg'  =>"已放入黑名单"
                ];
                return $data;
            }else{
                //防刷
                $count_key = 'count';
                $count =  Redis::get($count_key);
                if($count>10){
                    $data = [
                        'errno' => 50008,
                        'msg'   => "请求过于频繁"
                    ];
                    Redis::sadd('blacktoken',$token);
                    Redis::expire('blacktoken',600);
                    Redis::expire($count_key,30);
                    return $data;
                }else{
                    Redis::incr($count_key);
                    Redis::expire($count_key,30);
                    $user_id = Redis::get('user_id');
                    $user_info = P_users::find($user_id);
                    $data = [
                        'errno'  => 0,
                        'msg'    => 'ok',
                        'data'   => [
                            'user_info' => $user_info
                        ]
                    ];
                    return $data;
                }
            }
        }else{
            $data = [
                'errno'  => 400004,
                'msg'    => 'token无效 '
            ];
            return $data;
        }
    }
    public function sign()
    {
        $key = 'ss:user_sign'.date('ymd');
        $sign_count = Redis::zcard($key);
        if($sign_count>0){
            return '已签到';
        }else{
            $user_id =Redis::get('user_id');
            if(empty($user_id)){
                return '请登录';
            }
            Redis::zadd($key,time(),$user_id);
            return '签到成功';
        }
    }
}

