<?php

global $limbs, $users;

if (!$users->authenticated()) {
	jumpTo('home');
}

$user = $users->get($users->username());
$user = !empty($user) ? $user['0'] : false;

if (isset($_POST['email'])) {
  $form = array();
  foreach ($_POST as $field => $value) {
    if (empty($value)) { continue; }
    if (isset($user[$field]) && $user[$field] != $value) {
      $form[$field] = $value;
    }
  }

  if (empty($form) || $users->update($users->username(), $form)) {
    $parameters['messages'][] = 'Settings updated successully';
    $data = $users->get($users->username)['0'];
  } else {
    $data = $_POST;
  }
}

if (isset($_FILES['avatar']['name']) || isset($_FILES['cover']['name'])) {
  $avatar = $_FILES['avatar'];
  $cover = $_FILES['cover'];

  if (!empty($avatar['name'])) {
    if ($users->uploadAvatar($avatar)) {
      $parameters['messages'][] = 'Profile picture uploaded successully';
    }
  }

  if (!empty($cover['name'])) {
    if ($users->uploadCover($cover)) {
      $parameters['messages'][] = 'Channel cover uploaded successully';
    }
  }
}

$parameters['_section'] = 'user_dashboard';
$parameters['_title'] = 'User Settings';
$parameters['mainSection'] = $parameters['dashSection'] = 'settings';
$parameters['user'] = empty($data) ? $user : $data;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('settings.html', $parameters);