<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Models\GoodsModel;

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
    public function hash1()
    {
        $data = [
            'name1' =>"123456",
            'name2' => "99999"
        ];
        $key ='key1';
        Redis::hmset($key,$data);
    }
    public function hash2()
    {
        $a = Redis::hmget('key1',array('name1','name2'));
        var_dump($a);
    }
    public function goods(Request $request)
    {
        $goods_id = $request->get('goods_id');
        $key = 'goods_info:'.$goods_id;

        $info = Redis::hgetall($key);
        if(empty($info)){
            echo "商品ID：" . $goods_id;
            $g = GoodsModel::select('goods_id','goods_sn','goods_id','goods_name')->find($goods_id);
            echo '<pre>';print_r($g->toArray());echo '</pre>';
            $goods_info = $g->toArray();
            Redis::hmset($key,$goods_info);
        }else{
            echo "有缓存";
        }
        Redis::hincrby($key,'view_count',1);
        Redis::expire($key,7200);
    }

    /**
     * @param Request $request
     * 解密
     */
    public function dec(Request $request)
    {
        $method = 'AES-256-CBC';
        $key = 'api1911';
        $iv = '1616161616161616';
        $option = OPENSSL_RAW_DATA;

        echo '<pre>';print_r($_POST);echo '</pre>';
        $enc_data = base64_decode($_POST['data']);

        //解密数据
        $dec_data = openssl_decrypt($enc_data,$method,$key,$option,$iv);
        echo "解密数据：".$dec_data;
    }
    public function dec2(Request $request)
    {
        $enc_data=$_POST['data'];
        $priv_key = openssl_get_privatekey(file_get_contents(storage_path('keys/priv.key')));
        openssl_private_decrypt($enc_data,$dec_data,$priv_key);
        echo "解密的数据：". $dec_data;

    }

}
