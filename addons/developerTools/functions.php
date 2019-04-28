<?php

function getPhpVersion() {
  return substr(phpversion(), 0, 3);
}

function getMysqlVersion() {
  $output = shell_exec('mysql -V'); 
  preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
  return isset($version[0]) ? $version[0] : false;
}

function hasShellAccess() {
  return is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec');
}

function hasFfmpeg($path = 'which ffmpeg') {
  return shell_exec($path);
}

function hasFfprobe($path = 'which ffprobe') {
  return shell_exec($path);
}