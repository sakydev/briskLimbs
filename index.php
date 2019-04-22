<?php

$vendorFile = 'vendor/autoload.php';
if (!file_exists($vendorFile)) {
	exit("BriskLimbs dependencies are missing. Please make sure to run [composer install]");
}

require_once $vendorFile;
require_once 'config.php';

$request = array_filter(explode('/', trim($_SERVER['REQUEST_URI'])));
$base = basename(CORE_DIRECTORY);
$page = array_filter($request, function($c) use (&$base) {
	if ($base != $c && !strstr($c, '?') && !strstr($c, '&')) {
		return $c;
	}
});

define('IS_ADMIN', current($page) == 'admin' ? true : false);
$page = current($page) == 'watch' ? 'watch' : end($page);

global $database;
$limbs = new Limbs($database);
$users = new Users();
$limbs->stretch($page);