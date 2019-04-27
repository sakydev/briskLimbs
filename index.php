<?php

$vendorFile = 'vendor/autoload.php';
if (!file_exists($vendorFile)) {
	exit("BriskLimbs dependencies are missing. Please make sure to run [composer install]");
}

require_once $vendorFile;
require_once 'config.php';
global $database;

$page = isset($_GET['main']) ? $_GET['main'] : false;
$page = $page == 'admin' ? $next : $page;

define('IS_ADMIN', $page == 'admin' ? true : false);
if (!empty(($crumbs = explode('/', $_GET['crumbs'])))) {
	foreach ($crumbs as $key => $value) {
		$nextKey = $key + 1;
		if ($key % 2 == 0 && !empty($value) && isset($crumbs[$nextKey])) {
			$_GET[$value] = $crumbs[$nextKey];
		}
	}
}

$limbs = new Limbs($database);
$users = new Users();
$limbs->stretch($page);