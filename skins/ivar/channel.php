<?php

global $limbs;
$videos = new Videos();
$videos->initialize();

$ads = new Ads();

$size = 12;
$channel = isset($_GET['name']) ? $_GET['name'] : false;
if (!$channel) {
  $limbs->displayErrorPage( array(), 'Invalid channel name');
}

$userObject = new User($channel);
$user = $userObject->fetch();
$list = isset($_GET['list']) ? $_GET['list'] : 'all';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $size;
$limit = array($start, $size);

$listParameters = array();
$listParameters['uploader_name'] = $channel;
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

$parameters['page'] = $page;
$parameters['trending'] = $trending;
$parameters['ad'] = $ads->getByLocation('home_banner');
$parameters['sidebarAd'] = $ads->getByLocation('watch_sidebar');
$parameters['_title'] = 'Browse';
$parameters['results'] = $results;
$parameters['user'] = $user;
$parameters['user']['avatar'] = $userObject->getAvatar($channel);
$parameters['user']['cover'] = $userObject->getCover($channel);
$limbs->display('channel.html', $parameters);
