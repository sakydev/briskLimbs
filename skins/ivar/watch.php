<?php

global $limbs;

$videos = new Videos();
$videos->initialize();

$actions = new Actions();
$actions->initialize();

$comments = new Comments();
$comments->initialize();

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

$data['thumbnail'] = $thumbnails->highest();
$data['files'] = $files->get();

if ($related = $videos->list(array('keyword' => $video->title()))) {
  foreach ($related as $key => $vid) {
    $thumbnails = new Thumbnails($vid['filename'], directory($vid['date']), true);
    $related[$key]['thumbnail'] = $thumbnails->medium();
  }
}

$actions->watched($vKey);

$addedComments = $comments->list(array('vkey' => $vKey));
$totalComments = count($addedComments);
$parameters['video'] = $data;
$parameters['related'] = $related;
$parameters['comments'] = $addedComments;
$parameters['totalComments'] = $totalComments;
$parameters['_title'] = 'Watch ' . $video->title();
$parameters['_section'] = 'watch';
$limbs->display('watch.html', $parameters);
