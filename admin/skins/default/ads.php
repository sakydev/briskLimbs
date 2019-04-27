<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$ads = new Ads();

if (isset($_POST['name'])) {
	if ($ads->create($_POST)) {
		$parameters['message'] = 'Ad created successfully';
	} else {
		$parameters['ad'] = $_POST;
	}
}

if (isset($_GET['deactivate'])) {
	if ($ads->deactivate($_GET['deactivate'])) {
		$parameters['message'] = sprintf("Ad %s deactivated successfully", $_GET['deactivate']);
	}
}

if (isset($_GET['activate'])) {
	if ($ads->activate($_GET['activate'])) {
		$parameters['message'] = sprintf("Ad %s activated successfully", $_GET['activate']);
	}
}

if (isset($_GET['delete'])) {
	if ($ads->delete($_GET['delete'])) {
		$parameters['message'] = sprintf("Ad %s deleted successfully", $_GET['delete']);
	}
}

$list = isset($_GET['list']) ? $_GET['list'] : 'all';
$subSection = !empty($_GET['list']) ? ucfirst($_GET['list']) : 'All';
$listParameters = array();
# pr($listParameters);
$listParameters['sort'] = 'id';
$results = $ads->list($listParameters);

$totalFound = count($results);
$parameters['total'] = $total = $ads->count($listParameters);
$parameters['results'] = $results;
$parameters['mainSection'] = 'ads';
$parameters['action'] = $_GET['section'];

$parameters['_errors'] = $limbs->errors->collect();
$parameters['_title'] = 'Ads Manager - Dashboard';
$limbs->display('ads.html', $parameters);