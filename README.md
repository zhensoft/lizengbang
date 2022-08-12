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

```

