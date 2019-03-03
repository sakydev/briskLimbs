<?php

require_once '/var/www/html/limbs/config.php';
require MODEL_DIRECTORY . '/Conversion.php';
require MODEL_DIRECTORY . '/Logs.php';

if (count($argv) >=2 ) {
	foreach ($argv as $key => $argument) {
    if (strstr($argument, '=')) {
      $crumbs = array_filter(explode('=', $argument));
      if (isset($crumbs[0]) && isset($crumbs[1])) {
        $variable = $crumbs[0];
        $value = $crumbs[1];
        $$variable = $value;
      }
    }
  }

  $ffmpegPath = '/usr/bin/ffmpeg';
  $base = $directory . '/' . $filename;
  $path = TEMPORARY_DIRECTORY . '/' . $base . '.' . $extension;
  $log = LOGS_DIRECTORY . '/' . $base . '.log';
  $logs = new Logs($log);
  $logs->initialize();
  $conversion = new Conversion($ffmpegPath, $filename, $directory, $path, $logs);
  $results = $conversion->process();

  $status = 'successful';
  if ($results['details']) {
    $duration = $results['details']['duration'];
    $qualities = $results['details']['possibleQualities'];
  }

  if ($results['files']) {
    foreach ($results['files'] as $key => $file) {
      if ($file['status'] != 'success') {
        $status = $file['status'];
        break;
      }
    }
  }

  $fields = array('duration' => $duration, 'status' => $status, 'qualities' => $qualities);
  
  global $database;
  $videos = new Videos();
  $videos->initialize($database);
  $videos->multipleSet($fields, 'filename', $filename);

  @unlink($path);
}