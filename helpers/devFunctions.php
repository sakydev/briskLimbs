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

function getCurrentUrl() {
	return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
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
			$url = CORE_URL . '/signin/?return=' . getCurrentUrl();
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