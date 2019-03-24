<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

if (isset($_POST['file'])) {
	$path = $_POST['file'];
	if (file_exists($path)) {
		$response = array('status' => 'success', 'code' => file_get_contents($path));
	} else {
		$response = array('status' => 'error', 'message' => 'Path not found');
	}

	sendJsonResponse($response);
}

if (isset($_POST['list'])) {
	$path = $_POST['list'];
	if (file_exists($path)) {
		if (is_dir($path)) {
			$list = listFiles($path, false, 2);
			$response = array('status' => 'success', 'contents' => $list, 'code' => file_get_contents(current($list)));
		} else {
			$response = array('status' => 'error', 'contents' => false, 'message' => 'Invalid directory path');
		}
	} else {
		$response = array('status' => 'error', 'contents' => false, 'message' => 'Path not found');
	}

	sendJsonResponse($response);
}

$section = isset($_GET['main']) ? $_GET['main'] : false;
if ($section == 'addons' || $section == 'skins') {
	$items = listFiles(CORE_DIRECTORY . '/' . $section);
	if (is_dir(current($items))) {
		$parameters['subItems'] = listFiles(current($items), false, 2);
	}
}

$parameters['_title'] = 'Editor - Dashboard';
$parameters['mainSection'] = 'addons';
$parameters['section'] = $section;
$parameters['items'] = isset($items) ? $items : false;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('editor.html', $parameters);