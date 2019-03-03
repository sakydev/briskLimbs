<?php

global $users;
if (!$users->isAdmin()) {
  jumpTo('home');
}

$path = ADDONS_DIRECTORY . '/' . str_replace('|', '/', $_GET['path']);
if (strstr($path, '?')) {
  $path = substr($path, 0, strpos($path, '?'));
}

if (file_exists($path)) {
  require $path;
} else {
  global $limbs;

  $parameters['_errors'] = array("File not found at $path");
  $parameters['_title'] = 'File not found';
  $limbs->display('blank.html', $parameters);
}