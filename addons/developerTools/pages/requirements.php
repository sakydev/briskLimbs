<?php

$addons = new Addons();
$settings = $addons->limbs->settings;

$ffmpegPath = $settings->get('ffmpeg');
$ffprobePath = $settings->get('ffprobe');

$requirments = json_decode(file_get_contents(CORE_DIRECTORY . '/configs/requirments.json'), true);
$response = array();

$phpVersion = getPhpVersion();
$phpStatus = $phpVersion >= $requirments['php'] ? 'Ok' : $requirments['php'] . ' or higher required';

$mysqlVersion = getMysqlVersion();
$mysqlStatus = $mysqlVersion >= $requirments['mysql'] ? 'Ok' : $requirments['mysql'] . ' or higher required';

$shell = hasShellAccess() ? 'Ok' : 'Disabled';
$ffmpegStatus = ($ffmpeg = hasFfmpeg($ffmpegPath)) ? 'Ok' : 'Not found';
$ffprobeStatus = ($ffprobe = hasFfprobe($ffprobePath)) ? 'Ok' : 'Not found';

$response['php'] = array('version' => $phpVersion, 'status' => $phpStatus);
$response['mysql'] = array('version' => $mysqlVersion, 'status' => $mysqlStatus);
$response['shell_exec'] = array('status' => $shell);
$response['ffmpeg'] = array('path' => $ffmpegPath, 'status' => $ffmpegStatus);
$response['ffprobe'] = array('path' => $ffprobePath, 'status' => $ffprobeStatus);

$parameters['response'] = $response;
$parameters['_title'] = 'Requirements Check';
$parameters['mainSection'] = 'developer_tools';
$addons->display(DEV_CORE_NAME, 'pages/requirements.html', $parameters);