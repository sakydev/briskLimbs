<?php

$vendorFile = 'vendor/autoload.php';
if (!file_exists($vendorFile)) {
	exit("BriskLimbs dependencies are missing. Please make sure to run [composer install]");
}
require_once $vendorFile;
require_once 'config.php';

$page = isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : false;
if (substr_count($page, '/') > 1) {
	$params = array_filter(explode('/', $page));
	$page = current($params);
	$next = next($params);
}

define('IS_ADMIN', $page == 'admin' ? true : false);
$page = $page == 'admin' ? $next : $page;
global $database;
$limbs = new Limbs($database);
$users = new Users();
$limbs->stretch($page);