<?php

global $limbs, $users;

$videos = new Videos();
$videos->initialize();
$authenticated = $users->authenticated();
if ($authenticated) {
	if (!empty($_FILES['uploadMedia'])) {
		if ($status = $videos->upload($_FILES['uploadMedia'])) {
			sendJsonResponse(array('status' => 'success', 'filename' => $status['filename'], 'command' => $status['command']));
		} else {
			sendJsonResponse(array('status' => 'error', 'message' => $limbs->errors->collect()));
		}
	}

	if (isset($_POST['insert'])) {
		$form = $_POST;
		$form['title'] = $_POST['insert'];
		$form['uploader_name'] = $users->username();
		$form['uploader_id'] = $users->userId();
		unset($form['insert']);
		if ($status = $videos->insert($form)) {
			sendJsonResponse(array('status' => 'success', 'id' => $status));
		} else {
			sendJsonResponse(array('status' => 'error', 'message' => $limbs->errors->collect()));
		}
	}

	if (isset($_POST['update'])) {
		$videoId = $_POST['update'];
		$details['title'] = isset($_POST['title']) ? $_POST['title'] : false;
		$details['description'] = isset($_POST['description']) ? $_POST['description'] : false;
		if ($videos->update($videoId, $details)) {
			sendJsonResponse(array('status' => 'success', 'message' => 'Video updated'));
		} else {
			sendJsonResponse(array('status' => 'error', 'message' => $limbs->errors->collect()));
		}
	}
}

$parameters['_section'] = 'upload';
$parameters['_title'] = 'Upload Video';
if (!$limbs->settings->allowUploads()) {
	$limbs->displayErrorPage($parameters, 'Uploading is not allowed at the moment');
} elseif (!$authenticated) {
	$limbs->displayErrorPage($parameters, 'You must login before uploading');
} else {
	$limbs->display('upload.html', $parameters);
}
