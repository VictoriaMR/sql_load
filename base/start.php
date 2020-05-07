<?php

$config = require APP_PATH . 'base/env.php';

require APP_PATH . 'base/sqllog.class.php';

$action = $_GET['action'] ?? 'get';
$type = $_GET['type'] ?? key($config);

//站点列表
$siteList = array_filter(array_keys($config));

$filename = $config[$type] ?? '';

$sqlLog = new SqlLog($filename, $action);

$data = $sqlLog->index();

include(APP_PATH.'html/content.php');

// print_r($data);die();


