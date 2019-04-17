<?php

ini_set('display_errors', 'On');
error_reporting(-1);
require 'functions.php';

if ( file_exists(__DIR__ . '/../configs/db.php'))
{
    $pageTitle = 'Already Installed';
    require 'pages/header.php';
    require 'pages/installation_exists.php';
    require 'pages/footer.php';
    // header("Location: installer/install.php");
    exit;
}

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