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
	}
}

if (isset($_FILES['watermark'])) {
	$watermarkPath = MEDIA_DIRECTORY . '/watermark.png';
	echo $watermarkPath;
	if (file_exists($watermarkPath)) {
		unlink($watermarkPath);
	}

	if (move_uploaded_file($_FILES['watermark']['tmp_name'], $watermarkPath)) {
		$parameters['message'] = 'Watermark uploaded successfully';
	} else {
		$limbs->errors->add('Unable to upload watermark @ ' . $watermarkPath);
	}
}

$parameters['_title'] = 'Settings - Dashboard';
$parameters['mainSection'] = 'settings';
$parameters['settings'] = $settings->reFetch();
$parameters['type'] = isset($_GET['type']) ? $_GET['type'] : 'general';
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('settings.html', $parameters);