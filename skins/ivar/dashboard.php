<?php

global $limbs, $users;

if (!$users->authenticated()) {
  jumpTo('home');
}

$parameters['_section'] = 'user_dashboard';
$parameters['subSection'] = 'dashboard';
$parameters['_title'] = 'User Dashboard';
$limbs->display('dashboard.html', $parameters);
