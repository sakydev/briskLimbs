<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$addons = new Addons();
if (isset($_POST['bulk-action'])) {
	$addonNames = explode(',', trim($_POST['bulk-keys'], ','));
	switch ($_POST['bulk-action']) {
		case 'deactivate':
			if ($addons->bulkDeactivate($addonNames)) {
				$parameters['message'] = "Selected addons have been deactivated";
			} else {
				$parameters['message'] = "Something went wrong trying deactivate to selected addons";
			}
			break;
		case 'activate':
			if ($addons->bulkActivate($addonNames)) {
				$parameters['message'] = "Selected addons have been activated";
			} else {
				$parameters['message'] = "Something went wrong trying activate to selected addons";
			}
			break;
		case 'uninstall':
			if ($addons->bulkUninstall($addonNames)) {
				$parameters['message'] = "Selected addons have been deleted";
			} else {
				$parameters['message'] = "Something went wrong trying delete to selected addons";
			}
			break;
		
		default:
			break;
	}
}

if (isset($_GET['deactivate'])) {
	if ($addons->deactivate($_GET['deactivate'])) {
		$parameters['message'] = sprintf("Addon %s deactivated successfully", $_GET['deactivate']);
	}
}

if (isset($_GET['activate'])) {
	if ($addons->activate($_GET['activate'])) {
		$parameters['message'] = sprintf("Addon %s activated successfully", $_GET['activate']);
	}
}

if (isset($_GET['uninstall'])) {
	if ($addons->uninstall($_GET['uninstall'])) {
		$parameters['message'] = sprintf("Addon %s uninstalled successfully", $_GET['uninstall']);	
	}
}

if (isset($_GET['install'])) {
	if ($addons->install($_GET['install'])) {
		$parameters['message'] = sprintf("Addon %s installed successfully", $_GET['install']);		
	}
}

$list = isset($_GET['list']) ? $_GET['list'] : 'all';
switch ($list) {
	case 'active':
	case 'inactive':
		$listParameters['status'] = $list;
		break;
	
	default:
		break;
}


$size = 10;
$list = isset($_GET['list']) ? $_GET['list'] : 'all';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $size;
$limit = array($start, $size);

$subSection = !empty($_GET['list']) ? ucfirst($_GET['list']) : 'All';
$listParameters = array();

switch ($list) {
	case 'active':
	case 'inactive':
		$listParameters['status'] = $list;
		break;
	
	default:
		break;
}

if (isset($_GET['advanced-search'])) {
	$searchFields = array('name', 	'author_name');
	$searchOperators = array('=', '>', '<');
	foreach ($searchFields as $key => $field) {
		if (!empty($_GET[$field])) {
			$listParameters[$field] = $parameters['srch_' . $field] = $fieldValue = $_GET[$field];
			foreach ($searchOperators as $key => $char) {
				if (strstr($fieldValue, $char)) {
					$listParameters[$field] = array(str_replace($char, '', $fieldValue), $char);
				}
			}
		}
	}
}

# pr($listParameters);
$listParameters['sort'] = 'id';
$listParameters['limit'] = $limit;
$results = $addons->list($listParameters);
$totalFound = count($results);
$parameters['total'] = $total = $addons->count($listParameters);
$parameters['start'] = $start;
$parameters['end'] = $start + ($totalFound < $size ? $totalFound : $size);
$parameters['results'] = $results;
$parameters['available'] = $addons->idle();
$parameters['mainSection'] = 'addons';
$parameters['subSection'] = $subSection;

$parameters['pagination'] = buildPagination($page, $size, $total);
$parameters['_errors'] = array_merge($addons->errors->collect(), $limbs->errors->collect());
$parameters['_title'] = 'Addons Manager - Dashboard';
$limbs->display('addons.html', $parameters);