<?php

global $limbs, $users;

if ($users->authenticated()) {
	jumpTo('home');
}

if (!empty($_POST['username'])) {
	if ($users->create($_POST)) {
		/*if ($users->login($_POST['username'], $_POST['password'])) {
			jumpTo('home');
		}
		jumpTo('signin');*/
	} else {
		$parameters['_errors'] = $limbs->errors->collect();
		$parameters['postFields'] = $_POST;
	}
}

$parameters['_title'] = 'Signup';
if ($limbs->settings->allowSignups()) {
	$limbs->display('signup.html', $parameters);
} else {
	$parameters['messages'] = array('Signups are not allowed at the moment');
	$limbs->display('blank.html', $parameters);
}
