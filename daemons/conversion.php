<?php

require_once '/var/www/html/limbs/config.php';
require MODEL_DIRECTORY . '/Conversion.php';

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
  $path = TEMPORARY_DIRECTORY . '/' . $directory . '/' . $filename . '.' . $extension;
  $conversion = new Conversion($ffmpegPath, $filename, $directory, $path, $log);
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

  file_put_contents('ff.txt', print_r($status, true));
  file_put_contents('fields.txt', print_r($fields, true));
  
  global $database;
  $videos = new Videos();
  $videos->initialize($database);
  $videos->multipleSet($fields, 'filename', $filename);

  @unlink($path);
}