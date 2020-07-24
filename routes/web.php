<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/userinfo','TestController@userInfo');
Route::get('/hash1','TestController@hash1');
Route::get('/hash2','TestController@hash2'); //哈希添加多个
Route::get('/goods','TestController@Goods'); //商品



Route::post('/user/reg','Reg\RegController@reg'); //注册
Route::post('/user/login','Reg\RegController@login'); //登录
Route::get('/user/center','Reg\RegController@center')->middleware('accesstoken'); //个人中心
Route::get('/user/sign','Reg\RegController@sign')->middleware('viewcount');//签到

Route::get('/user/center2','Reg\RegController@center2')->middleware('accesstoken','viewcount');//测试

Route::post('/dec','TestController@dec');//对称解密
Route::post('/dec2','TestController@dec2');//非对称解密

Route::get('sign1','TestController@sign1');//验签
