<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

if (isset($_POST['settings'])) {
	unset($_POST['settings']);
	if (isset($_POST['video_codec'])) {
	  $qualities = array('240', '360', '480', '720', '1080');
	  foreach ($qualities as $key => $value) {
	    $checkKey = "quality_{$value}";
	    $_POST[$checkKey] = isset($_POST[$checkKey]) ? 'yes' : 'no';
	  }
	}

	if ($settings->bulkSet($_POST)) {
		$parameters['messages'][] = 'Settings updated successfully';
	}
}

if (!empty($_FILES['watermark']['tmp_name'])) {
	$watermarkPath = MEDIA_DIRECTORY . '/watermark.png';
	echo $watermarkPath;
	if (file_exists($watermarkPath)) {
		unlink($watermarkPath);
	}

	if (move_uploaded_file($_FILES['watermark']['tmp_name'], $watermarkPath)) {
		$parameters['messages'][] = 'Watermark uploaded successfully';
	} else {
		$limbs->errors->add("Unable to upload watermark @ $watermarkPath");
	}
}

$clips = array('pre', 'post');
foreach ($clips as $key => $type) {
	if (!empty($_FILES["{$type}_clip"]['tmp_name'])) {
		$path = MEDIA_DIRECTORY . "/{$type}.mp4";
		if (file_exists($path)) { @unlink($path); }
		if (move_uploaded_file($_FILES["{$type}_clip"]['tmp_name'], $path)) {
			$parameters['messages'][] = "{$type} clip uploaded successfully";
		} else {
			$limbs->errors->add("Unable to upload clip @ $path");
		}
	}
}

if (isset($_POST['test-email'])) {
	$to = $_POST['test_recipient'];
	$toName = $_POST['test_name'];
	$message = $_POST['test_message'];
	$subject = $_POST['test_subject'];

	$mail = new Mail();
	if ($mail->send($subject, $message, $to, $toName)) {
		$parameters['messages'][] = "Message sent to $toName ($to)";
	} else {
		$parameters['messages'][] = "Failed to send to $toName ($to)";
	}
}
$preClip = MEDIA_DIRECTORY . '/pre.mp4';
$postClip = MEDIA_DIRECTORY . '/post.mp4';
$parameters['_title'] = 'Settings - Dashboard';
$parameters['mainSection'] = 'settings';
$parameters['settings'] = $settings->reFetch();
$parameters['type'] = isset($_GET['type']) ? $_GET['type'] : 'general';
$parameters['preClip'] = file_exists($preClip) ? $preClip : 'No existing clip';
$parameters['postClip'] = file_exists($postClip) ? $postClip : 'No existing clip';
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('settings.html', $parameters);