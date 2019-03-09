<?php

global $limbs;
$videos = new Videos();
$videos->initialize();

$ads = new Ads();
/*$params = array(
	'uploader_name' => 'frank',
	'views' => array('250', '<')
);*/

$trending = $videos->getTrending(4);
foreach ($trending as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $trending[$key]['thumbnail'] = $thumbnails->medium();
  $trending[$key]['trunc_title'] = substr($trending[$key]['title'], 0, 38) . ' ..';
}

$fresh = $videos->getFresh(6);
foreach ($fresh as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $fresh[$key]['thumbnail'] = $thumbnails->medium();
  $fresh[$key]['trunc_title'] = substr($fresh[$key]['title'], 0, 22) . ' ..';
}

$parameters['ad'] = $ads->getByLocation('home_banner');
$parameters['_title'] = 'Home';
$parameters['trending'] = $trending;
$parameters['fresh'] = $fresh;
$limbs->display('home.html', $parameters);
