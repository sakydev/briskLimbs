<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->authenticated()) {
	jumpTo('home');
}

$key = $_GET['key'];

$videos = new Videos();
$videos->initialize();

if (isset($_POST['title'])) {
  if ($videos->update($key, $_POST)) {
    $parameters['message'] = 'Video updated successfully';
  } else {
    $data = $_POST;
  }
}

$parameters['_title'] = 'Edit - Dashboard';
$parameters['_section'] = 'user_dashboard';
$parameters['mainSection'] = 'edit';
$parameters['dashSection'] = 'videos';
$parameters['video'] = empty($data) ? $videos->get($key) : $data;
$parameters['_errors'] = $limbs->errors->collect();

if (!$videos->owns($users->username(), $key)) {
  $limbs->displayErrorPage($parameters, "You don't have permissions to edit this video");
} else {
  $limbs->display('edit.html', $parameters);
}