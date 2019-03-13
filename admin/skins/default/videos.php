<?php

global $limbs, $users;
$settings = $limbs->settings;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$videos = new Videos();
$videos->initialize();

if (isset($_POST['bulk-action'])) {
	$vKeys = explode(',', trim($_POST['bulk-keys'], ','));
	switch ($_POST['bulk-action']) {
		case 'deactivate':
			if ($videos->bulkDeactivate($vKeys)) {
				$parameters['message'] = "Selected videos have been deactivated";
			} else {
				$parameters['message'] = "Something went wrong trying deactivate to selected videos";
			}
			break;
		case 'activate':
			if ($videos->bulkActivate($vKeys)) {
				$parameters['message'] = "Selected videos have been activated";
			} else {
				$parameters['message'] = "Something went wrong trying activate to selected videos";
			}
			break;
		case 'delete':
			if ($videos->bulkDelete($vKeys)) {
				$parameters['message'] = "Selected videos have been deleted";
			} else {
				$parameters['message'] = "Something went wrong trying delete to selected videos";
			}
			break;
		
		default:
			break;
	}
}

if (isset($_GET['unfeature'])) {
	if ($videos->unfeature($_GET['unfeature'])) {
		$parameters['message'] = sprintf("Video %s unfeatured successfully", $_GET['unfeature']);
	}
}

if (isset($_GET['feature'])) {
	if ($videos->feature($_GET['feature'])) {
		$parameters['message'] = sprintf("Video %s featured successfully", $_GET['feature']);
	}
}

if (isset($_GET['deactivate'])) {
	if ($videos->deactivate($_GET['deactivate'])) {
		$parameters['message'] = sprintf("Video %s deactivated successfully", $_GET['deactivate']);
	}
}

if (isset($_GET['activate'])) {
	if ($videos->activate($_GET['activate'])) {
		$parameters['message'] = sprintf("Video %s activated successfully", $_GET['activate']);
	}
}

if (isset($_GET['delete'])) {
	if ($videos->delete($_GET['delete'])) {
		$parameters['message'] = sprintf("Video %s deleted successfully", $_GET['delete']);	
	}
}
$size = 25;
$list = isset($_GET['list']) ? $_GET['list'] : 'all';
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $size;
$limit = array($start, $size);

$subSection = !empty($_GET['list']) ? ucfirst($_GET['list']) : 'All';
$listParameters = array();

switch ($list) {
	case 'trending':
		$listParameters['sort'] = 'views';
		break;
	case 'active':
	case 'inactive':
		$listParameters['state'] = $list;
		break;
	case 'featured':
		$listParameters['featured'] = 'yes';
		break;
	case 'successful':
	case 'pending':
	case 'failed':
		$listParameters['status'] = $list;
		break;
	
	default:
		break;
}

if (isset($_GET['advanced-search'])) {
	$searchFields = array('keyword', 	'title', 	'uploader_name', 	'filename', 	'duration', 	'views', 	'published');
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
$results = $videos->list($listParameters);
foreach ($results as $key => $video) {
	$thumbnails = new Thumbnails($video['filename'], directory($video['date']), true);
	$results[$key]['thumbnail'] = $thumbnails->medium();
	$results[$key]['truc_title'] = substr($results[$key]['title'], 0, 38) . ' ..';
}

$totalFound = count($results);

$parameters['total'] = $total = $videos->count($listParameters);
$parameters['start'] = $start;
$parameters['end'] = $start + ($totalFound < $size ? $totalFound : $size);
$parameters['results'] = $results;
$parameters['mainSection'] = 'videos';
$parameters['subSection'] = $subSection;
$parameters['pagination'] = buildPagination($page, $size, $total);
$parameters['_errors'] = $limbs->errors->collect();
$parameters['_title'] = 'Videos Manager - Dashboard';

$limbs->display('videos.html', $parameters);