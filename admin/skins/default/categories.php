<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$categories = new Categories();

if (isset($_POST['name']) && !isset($_POST['edit'])) {
	if ($categories->create($_POST)) {
		$parameters['message'] = 'Category created successfully';
	} else {
		$parameters['category'] = $_POST;
	}
}

if (isset($_POST['edit'])) {
	unset($_POST['edit']);
	if ($categories->update($_GET['id'], $_POST)) {
		$parameters['message'] = 'Category updated successfully';
	}
}

if (isset($_GET['deactivate'])) {
	if ($categories->deactivate($_GET['deactivate'])) {
		$parameters['message'] = sprintf("Category %s deactivated successfully", $_GET['deactivate']);
	}
}

if (isset($_GET['activate'])) {
	if ($categories->activate($_GET['activate'])) {
		$parameters['message'] = sprintf("Category %s activated successfully", $_GET['activate']);
	}
}

if (isset($_GET['delete'])) {
	if ($categories->delete($_GET['delete'])) {
		$parameters['message'] = sprintf("Category %s deleted successfully", $_GET['delete']);
	}
}

$list = isset($_GET['list']) ? $_GET['list'] : 'all';
$subSection = !empty($_GET['list']) ? ucfirst($_GET['list']) : 'All';
$listParameters = array();
$listParameters['sort'] = 'id';
$results = $categories->list($listParameters);

$totalFound = count($results);
$parameters['total'] = $total = $categories->count($listParameters);
$parameters['results'] = !isset($_GET['id']) ? $results : $categories->get($_GET['id']);
$parameters['mainSection'] = 'categories';
$parameters['action'] = $_GET['action'];

$parameters['_errors'] = $limbs->errors->collect();
$parameters['_title'] = 'Categories Manager - Dashboard';
$limbs->display('categories.html', $parameters);