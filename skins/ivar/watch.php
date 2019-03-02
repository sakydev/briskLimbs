<?php

global $limbs;

$videos = new Videos();
$videos->initialize();

$actions = new Actions();
$actions->initialize();

$vKey = basename($_GET['request']);
$video = new Video($vKey);
$data = $video->fetch();

$thumbnails = new Thumbnails($video->filename(), $video->directory(), true);
$files = new Files($video->filename(), $video->directory(), true);

$data['thumbnail'] = $thumbnails->highest();
$data['files'] = $files->get();

if ($related = $videos->list(array('keyword' => $video->title()))) {
  foreach ($related as $key => $vid) {
    $thumbnails = new Thumbnails($vid['filename'], directory($vid['date']), true);
    $related[$key]['thumbnail'] = $thumbnails->medium();
  }
}

$actions->watched($vKey);
$parameters['video'] = $data;
$parameters['related'] = $related;
$parameters['_title'] = 'Watch ' . $video->title();
$parameters['_section'] = 'watch';
$limbs->display('watch.html', $parameters);
