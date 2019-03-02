<?php

global $limbs;
$videos = new Videos();
$videos->initialize();

/*$params = array(
	'uploader_name' => 'frank',
	'views' => array('250', '<')
);*/

$trending = $videos->getTrending(4);
foreach ($trending as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $trending[$key]['thumbnail'] = $thumbnails->medium();
}

$fresh = $videos->getFresh(6);
foreach ($fresh as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $fresh[$key]['thumbnail'] = $thumbnails->medium();
}


$parameters['_title'] = 'Home';
$parameters['trending'] = $trending;
$parameters['fresh'] = $fresh;
$limbs->display('home.html', $parameters);
