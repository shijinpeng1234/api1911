<?php

namespace App\Http\Controllers\Reg;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\P_users;

class RegController extends Controller
{
    public function reg(Request $request)
    {
        $user_name = $request->post('user_name');
        $password = $request->post('password');
        $email = $request->post('email');
        $reg_time = time();
        if(empty($user_name)){
            $data = [
                'errno' => 00001,
                'msg'   => "用户名不能为空"
            ];
            return $data;
        }
        if(empty($password)){
            $data = [
                'errno' => 00002,
                'msg'   => "密码不能为空"
            ];
            return $data;
        }
        if(empty($email)){
            $data = [
                'errno'  =>  00003,
                'msg'    =>  "Email不能为空"
            ];
            return $data;
        }
        $p_usersModel = new P_users();
        $p_usersModel ->user_name =$user_name;
        $p_usersModel ->password  = md5($password);
        $p_usersModel ->email =$email;
        $p_usersModel ->reg_time=$reg_time;
        $add =$p_usersModel->save();
        if($add){
            $data = [
                'errno'  =>  00004,
                'msg'    =>  "添加成功"
            ];
            return $data;
        }else{
            $data = [
                'errno'  =>  00004,
                'msg'    =>  "添加失败"
            ];
            return $data;
        }
    }
    public function login(Request $request)
    {
        $user_name = $request->post('user_name');
        $password = md5($request->post('password'));
        if(empty($user_name)){
            $data = [
                'errno' => 00001,
                'msg'   => "用户名不能为空"
            ];
            return $data;
        }
        if(empty($password)){
            $data = [
                'errno' => 00002,
                'msg'   => "密码不能为空"
            ];
            return $data;
        }
        $p_usersModel = new P_users();
        $username = $p_usersModel::where(['user_name'=>$user_name])->first();
        if(!$username){
            $data = [
                'errno' => 00006,
                'msg'   => "用户名不存在"
            ];
            return $data;
        }
        if($username['password']==$password){
            $data = [
                'errno' => 00007,
                'msg'   => "登录成功"
            ];
            return $data;
        }
    }
}

