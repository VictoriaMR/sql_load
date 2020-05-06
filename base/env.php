<?php

$config = [];

if (is_file(APP_PATH . '.env')) {
    $config = array_filter(parse_ini_file(APP_PATH . '.env', true));
}

return $config;