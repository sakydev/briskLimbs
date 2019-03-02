<?php

$path = ADDONS_DIRECTORY . '/' . str_replace('|', '/', $_GET['path']);
if (file_exists($path)) {
  require $path;
} else {
  global $limbs;

  $parameters['_errors'] = array("File not found at $path");
  $parameters['_title'] = 'File not found';
  $limbs->display('blank.html', $parameters);
}