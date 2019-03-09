<?php

global $limbs;

$videos = new Videos();
$videos->initialize();

$actions = new Actions();
$actions->initialize();

$comments = new Comments();
$comments->initialize();

$ads = new Ads();

if (isset($_POST['comment'])) {
  if ($comments->add($_POST['comment'], $_POST['video'])) {
    sendJsonResponse(array('status' => 'success', 'message' => 'Comment has been added'));
  } else {
    sendJsonResponse(array('status' => 'error', 'message' => $limbs->errors->collect()));
  }
}

$vKey = basename($_GET['request']);
$video = new Video($vKey);
$data = $video->fetch();

$thumbnails = new Thumbnails($video->filename(), $video->directory(), true);
$files = new Files($video->filename(), $video->directory(), true);

if ($highestThumbnail = $thumbnails->highest()) {
  $data['thumbnail'] = $highestThumbnail;
} else {
  $data['thumbnail'] = $thumbnails->getDefault();
}

if ($files = $files->get()) {
  $data['files'] = $files;
} else {
  $data['files'] = $files->getDefault();
  $parameters['defaultFiles'] = true;
}

if ($sidebar = $videos->list(array('keyword' => $video->title()))) {
	$sidebarTitle = 'Similar Videos';
  foreach ($sidebar as $key => $vid) {
    $thumbnails = new Thumbnails($vid['filename'], directory($vid['date']), true);
    $sidebar[$key]['thumbnail'] = $thumbnails->medium();
  }
} else {
	$sidebarTitle = 'Fresh Videos';
	$sidebar = $videos->getFresh(8);
  foreach ($sidebar as $key => $vid) {
    $thumbnails = new Thumbnails($vid['filename'], directory($vid['date']), true);
    $sidebar[$key]['thumbnail'] = $thumbnails->medium();
  }
}

$actions->watched($vKey);

$addedComments = $comments->list(array('vkey' => $vKey));
$parameters['video'] = $data;
$parameters['sidebarTitle'] = $sidebarTitle;
$parameters['sidebar'] = $sidebar;
$parameters['comments'] = $addedComments;
$parameters['totalComments'] = $video->comments();

$parameters['bannerAd'] = $ads->getByLocation('watch_banner');
$parameters['sidebarAd'] = $ads->getByLocation('watch_sidebar');
$parameters['_title'] = 'Watch ' . $video->title();
$parameters['_section'] = 'watch';
$limbs->display('watch.html', $parameters);
