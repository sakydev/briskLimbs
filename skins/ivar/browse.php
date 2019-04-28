<?php

global $limbs;
$videos = new Videos();
$ads = new Ads();

$size = 12;
$list = isset($_GET['section']) ? $_GET['section'] : 'all';
$page = isset($_GET['crumbs']) ? $_GET['crumbs'] : 1;
$start = ($page - 1) * $size;
$limit = array($start, $size);

$listParameters = array();
$listParameters['limit'] = $limit;

$results = $videos->list($listParameters);
foreach ($results as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $results[$key]['thumbnail'] = $thumbnails->medium();
  $results[$key]['trunc_title'] = substr($results[$key]['title'], 0, 38);
}

if ($videos->count($listParameters) >= $size) {
  $parameters['next'] = $page + 1;
}

$trending = $videos->listTrending($limbs->settings->get('trending'));
foreach ($trending as $key => $video) {
  $thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
  $trending[$key]['thumbnail'] = $thumbnails->medium();
  $trending[$key]['trunc_title'] = substr($trending[$key]['title'], 0, 38);
}

$parameters['ad'] = $ads->getByLocation('home_banner');
$parameters['sidebarAd'] = $ads->getByLocation('watch_sidebar');
$parameters['_title'] = 'Browse';
$parameters['results'] = $results;
$parameters['trending'] = $trending;
$limbs->display('browse.html', $parameters);
