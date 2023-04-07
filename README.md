# lizengbang-sms

#### 介绍
礼赠帮增值服务对接php的composer包

#### 软件架构
软件架构说明
测试文件在tests下面，测试的时候，可以点击根目录的bat,然后放访问
先在根目录 composer install
http://localhost:8888/tests/sendsms.php
类似这样方法测试

#### 安装教程
composer require zhensoft/lizengbang  dev-master  


#### 使用说明

```php 
<?php

use lizengbang\Sms;
require "vendor/autoload.php";

$appid="10000010";
$appkey="w3f9116714b194c7fa696c6907b8d6910l"; //此秘钥只是示例，请使用正确秘钥
$api_url="http://lizengbang.waiwubang.com/";
$destnumbers="18601062631";
$msg="【真诚软件】您的短信验证码是1234";

$obj = new  Sms($api_url,$appid,$appkey);
$rs=$obj->sendsms($destnumbers,$msg);
print_r($rs);




use lizengbang\Track;
require "vendor/autoload.php";

$appid = "10000010";
$appkey = "w3f9116714b194c7fa696c6907b8d6910l"; //此秘钥只是示例，请使用正确秘钥
$api_url = "http://lizengbang.waiwubang.com/";
$obj = new  Track($api_url, $appid, $appkey);
$clientkeynum="6666666";
$call_back_url="http://".$_SERVER['HTTP_HOST']."/lizengbang_get_track?clientkeynum=".$clientkeynum;
$list=array();
$item[]=array("com"=>"shunfeng","num"=>"SF1698511072276","callBackUrl"=>$call_back_url);
$item[]=array("com"=>"shunfeng","num"=>"SF1650078452758","callBackUrl"=>$call_back_url);
$list=$item;
$rs = $obj->subscribe($list);
print_r($rs);


use lizengbang\Shorturl;
require "vendor/autoload.php";

$change_type="0";
$appid = "10000010";
$appkey = "w3f9116714b194c7fa696c6907b8d6910l"; //此秘钥只是示例，请使用正确秘钥
$api_url = "http://vplat.qiwubang.com/";
$obj = new  Shorturl($api_url, $appid, $appkey);
$rt_arr = $obj->get_short_url("http://www.baidu.com",$change_type);
print_r($rt_arr);
die;




```

