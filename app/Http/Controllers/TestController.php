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
        echo "解密的数据：". $dec_data;echo '</br>';

        //返回
        $data = "api返回数据";
        //使用公钥加密
        $content = file_get_contents(storage_path("keys/www_pub.key"));
        $pub_key=openssl_get_publickey($content);
        openssl_public_encrypt($data,$enc_data,$pub_key);
        echo "加密后".$enc_data.'<br>';
        //post数据
        $post_data = [
            'data'=>$enc_data
        ];
        //将加密的文件发送
        $url = 'http://www.1911.com/dec3';
        //curl初始
        $ch = curl_init();
        //设置参数
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_POST,1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        //发送请求
        $response = curl_exec($ch);
        echo $response;
        //提示错误
        $errno =curl_errno($ch);
        if($errno){
            $errmsg = curl_error($ch);
            var_dump($errmsg);
        }
        curl_close($ch);

    }
    public function sign1(Request $request)
    {
        $key = 'api1911';
        $data = $request->get('data');
        $sign_str = $request->get('sign');
        $sign_str2 = sha1($data.$key);
        if($sign_str!=$sign_str2){
            echo "验签失败";
        }else{
            echo "验签成功";
        }
    }
    public function sign2(Request $request)
    {
        $data=$_POST['data'];
        $sign_str = $_POST['sign'];
        $content2 = file_get_contents(storage_path("keys/www_pub.key"));
        $prikey2 = openssl_get_publickey($content2);
        $a = openssl_verify($data,$sign_str,$prikey2,OPENSSL_ALGO_SHA1);
        echo '<br>'; echo  $a;
        if($a!=1){
            echo "验签失败";
        }else{
            echo "验签成功";
        }

    }
}
