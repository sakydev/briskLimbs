<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

if (isset($_POST['list'])) {
	$path = $_POST['list'];
	if (file_exists($path)) {
		if (is_dir($path)) {
			$raw = glob("$path/*");
			$list = array();
			foreach ($raw as $key => $file) {
				$list[basename($file)] = $file;
			}
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
	$raw = glob(CORE_DIRECTORY . "/{$section}/*");
	$items = array();
	foreach ($raw as $key => $path) {
		$items[basename($path)] = $path;
	}

	if (is_dir(current($items))) {
		$path = current($items);
		$subItems = array();
		foreach (glob("$path/*") as $key => $path) {
			$subItems[basename($path)] = $path;
		}
		$parameters['subItems'] = $subItems;
	}
}

$parameters['_title'] = 'Editor - Dashboard';
$parameters['mainSection'] = 'addons';
$parameters['section'] = $section;
$parameters['items'] = isset($items) ? $items : false;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('editor.html', $parameters);