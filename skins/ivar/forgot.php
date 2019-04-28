<?php

global $limbs, $users;
if ($users->authenticated()) {
	jumpTo('home');
}
if (isset($_POST['password'])) {
	$userId = $users->resetCodeExists($_GET['code']);
	if ($users->resetPassword($userId, $_POST['password'])) {
		$parameters['messages'][] = 'Password has been saved';
		jumpTo('signin');
	} else {
		$parameters['_errors'][] = 'Reset code is invalid';
	}
}

if (!empty($_POST['user'])) {
	$user = $_POST['user'];
	if ($users->userExists($user)) {
		if ($users->requestResetPassword($user)) {
			$parameters['messages'][] = 'Please check your email for reset link';
		}
	} else {
		$parameters['_errors'][] = 'User doesn\'t exist';
	}
} else {
	$parameters['postFields'] = $_POST;
}

$code = isset($_GET['code']) ? $_GET['code'] : false;
if ($code) {
	$user = $users->resetCodeExists($code);
	if ($user) {
		$parameters['restCodeExists'] = true;
	} else {
		$parameters['_errors'] = 'Reset code doesn\'t exist';
	}
}

$parameters['code'] = $code;
$parameters['_title'] = 'Forgot';
$limbs->display('forgot.html', $parameters);
