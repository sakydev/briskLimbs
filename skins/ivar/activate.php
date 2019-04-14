<?php

global $users;
if ($userid = $users->activationCodeExists($_GET['code'])) {
	if ($users->activate($userid)) {
		exit('Account activated. Please login <a href=' . CORE_URL . '/signin>here</a>');
	} else {
		exit('Unable to activate user. Please contact admin');
	}
} else {
	exit('Activation code not found or has expired');
}

