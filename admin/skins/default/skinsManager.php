<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$skins = new Skins();
if (isset($_GET['install'])) {
	if ($skins->install($_GET['install'])) {
		$parameters['message'] = sprintf("Skin %s installed successfully", $_GET['install']);		
	}
}

$results = $skins->list();
$totalFound = count($results);
$parameters['activeSkin'] = $limbs->settings->get('active_theme');
$parameters['results'] = $results;
$parameters['mainSection'] = 'skins';
$parameters['_errors'] = array_merge($skins->errors->collect(), $limbs->errors->collect());
$parameters['_title'] = 'Skins Manager - Dashboard';
$limbs->display('skinsManager.html', $parameters);