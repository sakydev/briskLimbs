<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$videos = new Videos();
$videos->initialize();

$uploads = $listParameters = array();
$listParameters['date'] = array('2019-03-04 00:00::00', '2019-03-04 23:59::59', 'between');

$uploads['today'] = $videos->count($listParameters);
$uploads['yesterday'] = $videos->count(array('date' => array(date("Y-m-d 00:00:00"), date("Y-m-d 00:00:00"), 'between')));

$parameters['mainSection'] = 'dashboard';
$parameters['_errors'] = $limbs->errors->collect();
$parameters['_title'] = 'Admin Dashboard';
$limbs->display('dashboard.html', $parameters);