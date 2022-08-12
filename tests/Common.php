<?php

//加载自己的源码
if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
//加载自己项目的依赖
if (is_file(__DIR__ . '/../vendor/autoload.php')) {

    require_once __DIR__ . '/../vendor/autoload.php';
}

