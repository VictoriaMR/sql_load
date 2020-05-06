<?php

/* 入口文件 */
die(123123);
define('APP_VERSION', '0.0.1');

// 定义应用目录
define('APP_PATH', dirname(__DIR__));

// 加载框架引导文件
require APP_PATH . '/base/start.php';