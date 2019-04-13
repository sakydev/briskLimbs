<?php

global $limbs, $users;

$videos = new Videos();
$actions = new Actions();
$comments = new Comments();
$ads = new Ads();

if (isset($_POST['comment'])) {
  if ($comments->add($_POST['comment'], $_POST['video'])) {
    sendJsonResponse(array('status' => 'success', 'message' => 'Comment has been added'));
  } else {
    sendJsonResponse(array('status' => 'error', 'message' => $limbs->errors->collect()));
  }
}

$categories = new Categories();

$vKey = basename($_GET['request']);
$video = new Video($vKey);
$data = $video->fetch();
$data['category'] = $categories->getNames(explode(',', $data['category']));
if (!$users->isAdmin() && $video->isPrivate() && $video->uploaderName() != $users->username()) {
  $limbs->displayErrorPage(array(), 'This video is private');
}

$thumbnails = new Thumbnails($video->filename(), $video->directory(), true);
$filesObject = new Files($video->filename(), $video->directory(), true);

if ($highestThumbnail = $thumbnails->highest()) {
  $data['thumbnail'] = $highestThumbnail;
} else {
  $data['thumbnail'] = $thumbnails->getDefault();
}

if ($files = $filesObject->get()) {
  $data['files'] = $files;
} else {
  $data['files'] = $filesObject->getDefault();
  $parameters['defaultFiles'] = true;
}

$sidebarLimit = $limbs->settings->get('related');

if ($sidebar = $videos->list(array('keyword' => $video->title(), 'limit' => $sidebarLimit))) {
	$sidebarTitle = 'Similar Videos';
  foreach ($sidebar as $key => $vid) {
    $thumbnails = new Thumbnails($vid['filename'], directory($vid['date']), true);
    $sidebar[$key]['thumbnail'] = $thumbnails->medium();
  }
} else {
	$sidebarTitle = 'Fresh Videos';
	$sidebar = $videos->listFresh($sidebarLimit);
  foreach ($sidebar as $key => $vid) {
    $thumbnails = new Thumbnails($vid['filename'], directory($vid['date']), true);
    $sidebar[$key]['thumbnail'] = $thumbnails->medium();
  }
}

$actions->watched($vKey);
$addedComments = $comments->list(array('vkey' => $vKey));
foreach ($addedComments as $key => $value) {
  $addedComments[$key]['thumbnail'] = $users->getAvatar($value['username']);
}

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
