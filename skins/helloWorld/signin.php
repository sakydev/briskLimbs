<?php

global $limbs, $users;

if ($users->authenticated()) {
	jumpTo('home');
}

if (!empty($_POST['username'])) {
	if ($users->login($_POST['username'], $_POST['password'])) {
		jumpTo('home');
	} else {
		$parameters['_errors'] = $limbs->errors->collect();
		$parameters['postFields'] = $_POST;
	}
}

$parameters['_title'] = 'Signin';
$limbs->display('signin.html', $parameters);
