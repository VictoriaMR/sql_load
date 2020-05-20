<?php

$config = require APP_PATH . 'base/env.php';

require APP_PATH . 'base/sqllog.class.php';

$action = $_GET['action'] ?? 'get';
$type = $_GET['type'] ?? key($config);
$page = $_GET['page'] ?? 1;
$pagesize = $_GET['size'] ?? 200;
$isAjax = $_GET['is_ajax'] ?? 0;

//站点列表
$siteList = array_filter(array_keys($config));

$filename = $config[$type] ?? '';

$sqlLog = new SqlLog($filename, $action);

$total = $sqlLog->getTotal();

$data = $sqlLog->index($page, $pagesize);


if ($isAjax) {
	print_r(json_encode(['list' => $data, 'total'=>$total], JSON_UNESCAPED_UNICODE));
	exit();
}

include(APP_PATH.'html/content.php');


