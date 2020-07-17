<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function userInfo()
    {
        phpinfo();
        exit;
        $url = 'http://www.1911.com/test1';
        $data = file_get_contents($url);
        echo $data;
    }
}
