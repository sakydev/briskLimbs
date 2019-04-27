<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$username = $_GET['section'];
if (isset($_POST['email'])) {
  if ($users->update($username, $_POST)) {
    $parameters['message'] = 'User updated successfully';
  } else {
    $data = $_POST;
  }
}

$parameters['_title'] = 'Edit User - Dashboard';
$parameters['mainSection'] = 'users';
$parameters['user'] = empty($data) ? $users->get($username) : $data;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('edituser.html', $parameters);