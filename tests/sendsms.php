<?php 
require_once __DIR__ . '/Common.php';

use   lizengbang\Sms;

$appid="10000010";
$appkey="1111111"; //此秘钥只是示例，请使用正确秘钥
$api_url="http://lizengbang.waiwubang.com/";
$destnumbers="18601062631";
$msg="【真诚软件】您的短信验证码是1234";
$sms = new  Sms($api_url,$appid,$appkey);
$rs=$sms->sendsms($destnumbers,$msg);   
print_r($rs);
