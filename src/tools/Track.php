<?php
// +----------------------------------------------------------------------
// | 王磊 [ Programming makes me happy ]
// +----------------------------------------------------------------------
// | Copyright (c) 2022 08 12  http://www.wlphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: wl < 613154514@qq.com >
// +----------------------------------------------------------------------
namespace lizengbang;
use think\Request;

class Track
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



    //订阅方法
    public function subscribe($list)
    {
        $action = "/prod-api/openapi/express/subscriberecord/moredata";
        $appid = $this->appid;
        $appkey = $this->appkey;
        $body = array();
        $body=$list;
        $url = $this->api_url . $action;
        $rs = $this->send_data($appid, $appkey, $body, $url);
        //解析出来返回数组
        $arr=json_decode($rs,1);
        if($arr['code']!='200'){
            $rt['sta']="0";
            $rt['msg']=$arr['msg'];
            return $rt;
        }
        $rt['sta']="1";
        $rt['msg']="订阅成功！";
        return $rt;
    }
	


    //接受推送方法
    public  function get_track (){
        //接受参数
        $request = Request::instance();
        $param = $request->param();
        //自己的url参数里面的get值也会接受
        $param_content=json_encode($param, JSON_UNESCAPED_UNICODE); //这个接受到包含url的get值
        $content = file_get_contents("php://input"); //后面这个接受到的是纯净数据
        $rt_arr=get_tuisong_do_data($param_content,$content);
        
        if($rt_arr['sta']!='1'){
            echo  '{ "msg":"接收成功,业务逻辑处理失败", "code": "0", "data": "接收成功,业务逻辑处理失败" }'; die;
        }
            echo  '{ "msg":"接收成功,处理业务成功", "code": "200", "data": "接收成功,处理业务成功" }'; die;
    }



    //获取剩余条数方法
    public function get_account_info()
    {
        $action = "/prod-api/openapi/express/subscriberecord/getyucount";
        $appid = $this->appid;
        $appkey = $this->appkey;
        $body = array();
        $url = $this->api_url . $action;
        $rs = $this->send_data($appid, $appkey, $body, $url);
        //解析出来返回数组
        $arr=json_decode($rs,1);
        if($arr['code']!='200'){
            $rt['sta']="0";
            $rt['msg']=$arr['msg'];
            return $rt;
        }
        $rt['sta']="1";
        $rt['msg']="获取成功！";
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
