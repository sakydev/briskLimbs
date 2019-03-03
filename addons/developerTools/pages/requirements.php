<?php

$addons = new Addons();

$requirments = json_decode(file_get_contents(CORE_DIRECTORY . '/configs/requirments.json'), true);
$response = array();

$phpVersion = getPhpVersion();
$status = $phpVersion >= $requirments['php'] ? 'Ok' : $requirments['php'] . ' or higher required';

$mysqlVersion = getMysqlVersion();
$status = $mysqlVersion >= $requirments['mysql'] ? 'Ok' : $requirments['mysql'] . ' or higher required';

$shell = hasShellAccess() ? 'Ok' : 'Disabled';
$ffmpegStatus = ($ffmpeg = hasFfmpeg()) ? 'Ok' : 'Not found';
$ffprobeStatus = ($ffprobe = hasFfprobe()) ? 'Ok' : 'Not found';

$response['php'] = array('version' => $phpVersion, 'status' => $status);
$response['mysql'] = array('version' => $mysqlVersion, 'status' => $status);
$response['shell_exec'] = array('status' => $shell);
$response['ffmpeg'] = array('path' => $ffmpeg, 'status' => $ffmpegStatus);
$response['ffprobe'] = array('path' => $ffprobe, 'status' => $ffprobeStatus);

$parameters['response'] = $response;
$parameters['_title'] = 'Requirements Check';
$parameters['mainSection'] = 'developer_tools';
$addons->display(DEV_CORE_NAME, 'pages/requirements.html', $parameters);