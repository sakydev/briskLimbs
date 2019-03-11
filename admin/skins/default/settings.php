<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

if (isset($_POST['settings'])) {
	unset($_POST['settings']);
  $qualities = array('240', '360', '480', '720', '1080');
  foreach ($qualities as $key => $value) {
    $checkKey = "quality_{$value}";
    $_POST[$checkKey] = isset($_POST[$checkKey]) ? 'yes' : 'no';
  }

	if ($settings->bulkSet($_POST)) {
		$parameters['message'] = 'Settings updated successfully';
	} else {
		$parameters['_errors'] = array('Something went wrong trying to update settings');
	}
}

$parameters['_title'] = 'Settings - Dashboard';
$parameters['mainSection'] = 'settings';
$parameters['settings'] = $settings->reFetch();
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('settings.html', $parameters);