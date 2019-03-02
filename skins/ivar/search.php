<?php

global $limbs;

$videos = new Videos();
$videos->initialize();

if (isset($_GET['keyword'])) {
	$keyword = $_GET['keyword'];
}

$size = 10;
$list = isset($_GET['list']) ? $_GET['list'] : 'all';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $size;
$limit = array($start, $size);

$listParameters = array();

$listParameters['limit'] = $limit;
$listParameters['keyword'] = $keyword;

$results = $videos->list($listParameters);
if ($totalResults = count($results) >= $size) {
	$listParameters['next'] = $page + 1;
}
$parameters['results'] = $results;
$parameters['total'] = count($results);
$parameters['_title'] = 'Search results for ' . $keyword;
$limbs->display('search.html', $parameters);
