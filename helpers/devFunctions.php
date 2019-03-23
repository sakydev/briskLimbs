<?php

function prettyPrint($data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
}

function pr($data) {
	prettyPrint($data);
}

function prettyPrintExit($data, $message = false) {
	prettyPrint($data);
	$message = !$message ? 'Exit in ' . __FILE__ . ':@line ' . __LINE__ : $message;
	exit($message);
}

function pex($data, $message = false) {
	prettyPrintExit($data, $message);
}

function displayMessage($message) {
	echo php_sapi_name() === 'cli' ? "$message\n" : "$message<br>";
}

function jumpTo($path) {
	switch ($path) {
		case 'home':
			$url = CORE_URL;
			break;
		case 'signup':
			$url = CORE_URL . '/signup';
			break;
		case 'signin':
			$url = CORE_URL . '/signin';
			break;
		
		default:
			$url = $path;
			break;
	}

	header("Location: $url");
	exit;
}

function hrefTag($url, $text) {
	return "<a href='{$url}'>{$text}</a>";
}

function linkTag($url) {
	return "<link rel='stylesheet' type='text/css' href='{$url}'>";
}

function scriptTag($url) {
	return "<script type='text/javascript'>{$url}</script>";
}

function hook($function, $parameters = false) {
	if (function_exists($function)) {
		$function($parameters);
	}
}

function hookable($function) {
	return function_exists($function) ? true : false;
}