<?php

ini_set('display_errors', 'On');
error_reporting(-1);

require 'functions.php';
define('INSTALLER_CORE', __DIR__);
define('CORE_DIRECTORY', dirname(INSTALLER_CORE));
$section = isset($_GET['section']) ? $_GET['section'] : 'release';
$releaseData = json_decode(file_get_contents(INSTALLER_CORE . '/release.json'), true);
$currentInstallation = CORE_DIRECTORY . '/configs/release.json';
if (file_exists($currentInstallation)) {
  $installationData = json_decode(file_get_contents($currentInstallation), true);
  if ($installationData['version'] > $releaseData['version']) {
    $section = 'upgrade.php';
  }
}
if ($section == 'release') {
  $pageTitle = 'Release Information';
} else {
  $pageTitle = 'Requirements Check';
}

require 'pages/header.php';
require "pages/{$section}.php";
require 'pages/footer.php'; 