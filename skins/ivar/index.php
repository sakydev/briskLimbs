<?php

global $limbs;
$videos = new Videos();
$ads = new Ads();

$featured = $videos->list(array('featured' => 'yes'));
foreach ($featured as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $featured[$key]['thumbnail'] = $thumbnails->medium();
  $featured[$key]['trunc_title'] = substr($featured[$key]['title'], 0, 38);
}

$trending = $videos->listTrending($limbs->settings->get('trending'));
foreach ($trending as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $trending[$key]['thumbnail'] = $thumbnails->medium();
  $trending[$key]['trunc_title'] = substr($trending[$key]['title'], 0, 38);
}

$fresh = $videos->listFresh($limbs->settings->get('fresh'));
foreach ($fresh as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $fresh[$key]['thumbnail'] = $thumbnails->medium();
  $fresh[$key]['trunc_title'] = substr($fresh[$key]['title'], 0, 22);
}

$parameters['ad'] = $ads->getByLocation('home_banner');
$parameters['_title'] = 'Home';
$parameters['featured'] = $featured;
$parameters['trending'] = $trending;
$parameters['fresh'] = $fresh;
$limbs->display('home.html', $parameters);
