<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$vkey = $_GET['section'];
$videos = new Videos();
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
$parameters['mainSection'] = 'edit';
$parameters['video'] = $data;
$parameters['categories'] = $activeCategories;
$parameters['_errors'] = $limbs->errors->collect();
$limbs->display('edit.html', $parameters);