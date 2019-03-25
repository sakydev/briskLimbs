<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$parameters['mainSection'] = 'dashboard';
$parameters['_errors'] = $limbs->errors->collect();
$parameters['_title'] = 'Not found';
$limbs->display('error_404.html', $parameters);