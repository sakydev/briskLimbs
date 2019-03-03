<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
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
$parameters['mainSection'] = 'edit';
$parameters['video'] = empty($data) ? $videos->get($key) : $data;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('edit.html', $parameters);