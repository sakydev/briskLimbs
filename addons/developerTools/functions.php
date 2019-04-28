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

function hasFfmpeg($path) {
  return strstr(shell_exec("$path 2>&1"), 'ffmpeg version');
}

function hasFfprobe($path) {
  return strstr(shell_exec("$path --version 2>&1"), 'ffprobe version');
}