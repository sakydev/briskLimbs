<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->authenticated()) {
	jumpTo('home');
}

$vkey = $_GET['key'];

$videos = new Videos();
$videos->initialize();

$categories = new Categories();
$activeCategories = array();
foreach ($categories->listActive(20) as $key => $value) {
	$activeCategories[$value['id']] = $value;
}

if (isset($_POST['title'])) {
	$_POST['category'] = !empty($_POST['category']) ? implode(',', $_POST['category']) : 1;
  if ($videos->update($vkey, $_POST)) {
    $parameters['message'] = 'Video updated successfully';
  } else {
    $data = $_POST;
  }
}

$data = empty($data) ? $videos->get($vkey) : $data;
$cats = explode(',', $data['category']);
foreach ($cats as $key => $cat) {
	if (isset($activeCategories[$cat])) {
		$data['categoryNames'][] = $activeCategories[$cat]['name'];
	}
}

$parameters['_title'] = 'Edit - Dashboard';
$parameters['_section'] = 'user_dashboard';
$parameters['mainSection'] = 'edit';
$parameters['dashSection'] = 'videos';
$parameters['video'] = $data;
$parameters['categories'] = $activeCategories;
$parameters['_errors'] = $limbs->errors->collect();

if (!$videos->validatePermissions($key)) {
  $limbs->displayErrorPage($parameters, "You don't have permissions to edit this video");
} else {
  $limbs->display('edit.html', $parameters);
}