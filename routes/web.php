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

Route::post('/user/reg','Reg\RegController@reg');
Route::post('/user/login','Reg\RegController@login');
Route::post('/user/center','Reg\RegController@center');





