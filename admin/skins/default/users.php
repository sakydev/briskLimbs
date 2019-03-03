<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

if (isset($_POST['bulk-action'])) {
	$usernames = explode(',', trim($_POST['bulk-keys'], ','));
	switch ($_POST['bulk-action']) {
		case 'deactivate':
			if ($users->bulkDeactivate($usernames)) {
				$parameters['message'] = "Selected users have been deactivated";
			} else {
				$parameters['message'] = "Something went wrong trying deactivate to selected users";
			}
			break;
		case 'activate':
			if ($users->bulkActivate($usernames)) {
				$parameters['message'] = "Selected users have been activated";
			} else {
				$parameters['message'] = "Something went wrong trying activate to selected users";
			}
			break;
		case 'delete':
			if ($users->bulkDelete($usernames)) {
				$parameters['message'] = "Selected users have been deleted";
			} else {
				$parameters['message'] = "Something went wrong trying delete to selected users";
			}
			break;
		
		default:
			break;
	}
}

if (isset($_GET['deactivate'])) {
	if ($users->deactivate($_GET['deactivate'])) {
		$parameters['message'] = sprintf("User %s deactivated successfully", $_GET['deactivate']);
	}
}

if (isset($_GET['activate'])) {
	if ($users->activate($_GET['activate'])) {
		$parameters['message'] = sprintf("User %s activated successfully", $_GET['activate']);
	}
}

if (isset($_GET['delete'])) {
	if ($users->delete($_GET['delete'])) {
		$parameters['message'] = sprintf("User %s deleted successfully", $_GET['delete']);	
	}
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
		$listParameters['status'] = 'ok';
		break;
	case 'inactive':
	case 'banned':
		$listParameters['status'] = $list;
		break;
	
	default:
		$listParameters['sort'] = 'date';
		break;
}

if (isset($_GET['advanced-search'])) {
	$searchFields = array('username', 	'email', 	'level', 'date');
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
$listParameters['limit'] = $limit;
$results = $users->list($listParameters);
$totalFound = count($results);

$parameters['total'] = $total = $users->count($listParameters);
$parameters['start'] = $start;
$parameters['end'] = $start + ($totalFound < $size ? $totalFound : $size);
$parameters['results'] = $results;
$parameters['mainSection'] = 'users';
$parameters['subSection'] = $subSection;
$parameters['pagination'] = buildPagination($page, $size, $total);
$parameters['_errors'] = $limbs->errors->collect();
$parameters['_title'] = 'Users Manager - Dashboard';

$limbs->display('users.html', $parameters);