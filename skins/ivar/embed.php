<?php

global $limbs;

$videos = new Videos();
$actions = new Actions();

$vKey = $_GET['section'];
$video = new Video($vKey);
$data = $video->fetch();

$thumbnails = new Thumbnails($video->filename(), $video->directory(), true);
$files = new Files($video->filename(), $video->directory(), true);

$data['thumbnail'] = $thumbnails->highest();
$data['files'] = $files->get();

$actions->watched($vKey);
$parameters['video'] = $data;
$parameters['_title'] = 'Watch ' . $video->title();
$parameters['width'] = !empty($_GET['width']) ? $_GET['width'] : false;
$parameters['height'] = !empty($_GET['height']) ? $_GET['height'] : false;
$parameters['_section'] = 'embed';

$limbs->display('bricks/player.html', $parameters);
