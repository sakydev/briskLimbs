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