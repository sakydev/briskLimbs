<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$parameters['mainSection'] = 'dashboard';
$parameters['_errors'] = $limbs->errors->collect();
$parameters['_title'] = 'Admin Dashboard';
$limbs->display('dashboard.html', $parameters);