<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$key = $_GET['section'];

$videos = new Videos();
$fields = $videos->getFields($key, array('filename', 'date'));
$path = LOGS_DIRECTORY . '/' . directory($fields['date']) . '/' . $fields['filename'] . '.log';
$logs = new Logs($path);

if ($logs->exists()) {
  $parameters['log'] = $logs->read();
} else {
  $limbs->errors->add("No file found at $path");
}

$parameters['_title'] = 'Logs - Dashboard';
$parameters['mainSection'] = 'videos';
$parameters['key'] = $key;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('log.html', $parameters);