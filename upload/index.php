<?php
ini_set('date.timezone', 'Asia/Shanghai');
define('APP_MEMORY_START', memory_get_usage());
define('APP_TIME_START', microtime(true));
define('DS', '/');
define('ROOT_PATH', strtr(realpath(dirname(__FILE__).'/../').'/', '\\', '/'));
define('APP_DOMAIN', 'http://upload.example.com/');
require ROOT_PATH.'frame/start.php';