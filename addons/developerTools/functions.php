<?php

function getPhpVersion() {
  return substr(phpversion(), 0, 3);
}

function getMysqlVersion() {
  $output = shell_exec('mysql -V'); 
  preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
  return $version[0];
}

function hasShellAccess() {
  return is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec');
}

function hasFfmpeg() {
  return shell_exec('which ffmpeg');
}

function hasFfprobe() {
  return shell_exec('which ffprobe');
}