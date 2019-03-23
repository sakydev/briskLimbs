<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$section = isset($_GET['main']) ? $_GET['main'] : false;
if ($section == 'addons' || $section == 'skins') {
	$raw = glob(CORE_DIRECTORY . "/{$section}/*");
	$items = array();
	foreach ($raw as $key => $path) {
		$items[basename($path)] = $path;
	}
}

$parameters['_title'] = 'Editor - Dashboard';
$parameters['mainSection'] = 'addons';
$parameters['section'] = $section;
$parameters['items'] = isset($items) ? $items : false;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('editor.html', $parameters);