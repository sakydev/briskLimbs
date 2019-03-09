<?php

function adminHound() {
	global $users;
	if ($users->isAdmin()) {
		$addons = new Addons();
		$parameters['watchActions'] = strstr($_SERVER['REQUEST_URI'], '/watch/') ? true : false;
		$parameters['watchkey'] = basename($_SERVER['REQUEST_URI']);
		$addons->display(basename(__DIR__), 'navbar.html', $parameters);
	}
}