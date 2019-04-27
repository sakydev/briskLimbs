<?php

global $limbs, $users;

if ($users->authenticated()) {
	jumpTo('home');
}

if (!empty($_POST['username'])) {
	if ($users->login($_POST['username'], $_POST['password'])) {
		$redirect = !empty($_GET['redirect']) ? $_GET['redirect'] : 'home';
		jumpTo($redirect);
	} else {
		$parameters['_errors'] = $limbs->errors->collect();
		$parameters['postFields'] = $_POST;
	}
}

$parameters['_title'] = 'Signin';
$limbs->display('signin.html', $parameters);
