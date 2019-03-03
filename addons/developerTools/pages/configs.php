<?php

$addons = new Addons();

$response = array();
$settings = array('upload_max_filesize', 'max_execution_time', 'post_max_size', 'memory_limit');
foreach ($settings as $key => $value) {
  $response[$value] = ini_get($value);
}

$response['core_url'] = CORE_URL;
$response['core_directory'] = CORE_DIRECTORY;
$parameters['response'] = $response;
$parameters['_title'] = 'Server Configs';
$parameters['mainSection'] = 'developer_tools';
$addons->display(DEV_CORE_NAME, 'pages/configs.html', $parameters);