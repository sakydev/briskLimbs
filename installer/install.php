<?php
  
  ini_set('display_errors', 'On');
  error_reporting(-1);

  require 'functions.php';
  define('INSTALLER_CORE', __DIR__);
  define('CORE_DIRECTORY', dirname(INSTALLER_CORE));
  $section = isset($_GET['section']) ? $_GET['section'] : 'release';

  if ($section == 'release') {
    $pageTitle = 'Release Information';
  } else {
    $pageTitle = 'Requirements Check';
  }

  require 'pages/header.php';
  require "pages/{$section}.php";
  require 'pages/footer.php'; 