<?php
  
require 'functions.php';
$addons = new Addons();

define('DEV_CORE_PATH', __DIR__);
define('DEV_CORE_NAME', basename(DEV_CORE_PATH));
$pages = DEV_CORE_NAME . '/pages';
$menu = array(
  'developer_tools' => array(
    'display_name' => 'Developer Tools',
    'sub' => array(
      'PHP Info' => $pages . '/info.php',
      'Requirements' => $pages . '/requirements.php',
      'Server Configs' => $pages . '/configs.php'
    )
  )
);

$addons->addMenu($menu);