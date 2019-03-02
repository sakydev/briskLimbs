<?php
  
  $addons = new Addons();
  
  $pages = basename(__DIR__) . '|pages';
  $menu = array(
    'developer_tools' => array(
      'display_name' => 'Developer Tools',
      'sub' => array(
        'PHP Info' => $pages . '|info.php',
        'Requirements' => $pages . '|requirements.php',
        'Server Configs' => $pages . '|configs.php'
      )
    )
  );

  $addons->addMenu($menu);