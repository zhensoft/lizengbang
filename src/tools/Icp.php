<?php
// +----------------------------------------------------------------------
// | 王磊 [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2025 07 03  http://www.wlphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: wl < 613154514@qq.com >
// +----------------------------------------------------------------------
namespace lizengbang;

use think\Request;

class Icp
{
    public $api_url;
    public $appid;
    public $appkey;

    public function __construct($api_url = null, $appid = null, $appkey = null)
    {
        $this->api_url = $api_url;
        $this->appid = $appid;
        $this->appkey = $appkey;
    }

    public function test()
    {
        echo "test 执行了";
    }



    //获取icp备案信息
    public function get_icp($domain = "")
    {
        $action = "/prod-api/openapi/plat/beian/getInfo";
        $appid = $this->appid;
        $appkey = $this->appkey;
        $body = array();
        $body["url"] = $domain;
        $url = $this->api_url . $action;
        $rs = $this->send_data($appid, $appkey, $body, $url);
        //解析出来返回数组
        $arr = json_decode($rs, 1);
        if ($arr['code'] != '200') {
            $rt['sta'] = "0";
            $rt['msg'] = $arr['msg'];
            return $rt;
        }
        $rt['sta'] = "1";
        $rt['msg'] = $arr['msg'];
        $rt['data'] = $arr['data'];
        return $rt;
    }



    //具体发送方法
    public function send_data($appid, $appkey, $body, $url)
    {
        $arr['appId'] = $appid;
        if (!empty($body)) {
            $arr['param'] = $body;
            $param_str = json_encode($body, JSON_UNESCAPED_UNICODE);
            $param_str = str_replace("\\", "", $param_str);
        }
        $arr['nonceStr'] = $this->createNoncestr();
        $arr['timeStamp'] = time();
        $String = $arr['appId'] . $param_str . $arr['nonceStr'] . $arr['timeStamp'] . $appkey;
        //签名步骤三：MD5加密
        $String = md5($String);
        //签名步骤四：所有字符转为大写
        $sign = strtoupper($String);
        $arr['sign'] = $sign;
        $arr = json_encode($arr, JSON_UNESCAPED_UNICODE);
        $result = $this->curlPostJson($url, $arr);
        return $result;
    }



    /**
     *    作用：产生随机字符串，不长于32位
     */
    public function createNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }



    /**
     * @param $url 发送post请求的url
     * @param $jsonStr 发送的数据
     * @return mixed
     */
    public function curlPostJson($url, $jsonStr)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json; charset=utf-8',
                'Content-Length: ' . strlen($jsonStr)
            )
        );
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return $response;
    }
}
