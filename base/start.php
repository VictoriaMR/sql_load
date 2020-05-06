<?php

$config = require APP_PATH . 'base/env.php';

require APP_PATH . 'base/sqllog.class.php';

$action = $_GET['action'] ?? 'get';
$type = $_GET['type'] ?? key($config);

$filename = $config[$type];

$sqlLog = new SqlLog($filename, $action);

$data = $sqlLog->index();


