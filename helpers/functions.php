<?php

function createDirectories($directories = false) {
	if (!$directories) {
		$directories = array(
			LOGS_DIRECTORY, 
			TEMPORARY_DIRECTORY, 
			THUMBNAILS_DIRECTORY, 
			VIDEOS_DIRECTORY
		);
	}

	foreach ($directories as $key => $directory) {
		$yearDirectory = $directory . '/' . date('Y');
		$monthDirectory = $yearDirectory . '/' . date('m');
		$dayDirectory = $monthDirectory . '/' . date('d'); 

		@mkdir($yearDirectory);
		@mkdir($monthDirectory);
		@mkdir($dayDirectory);

		$combination = date('Y/m/d');
	}

	$response = array();
	$response['combination'] = $combination;
	$response['logs'] = LOGS_DIRECTORY . '/' . $combination;
	$response['temporary'] = TEMPORARY_DIRECTORY . '/' . $combination;
	$response['thumbnails'] = THUMBNAILS_DIRECTORY . '/' . $combination;
	$response['videos'] = VIDEOS_DIRECTORY . '/' . $combination;
	return $response;
}

function createDirectory($parent, $sub) {
	if (file_exists($parent)) {
		if (strstr($sub, '/')) {
			$crumbs = explode('/', $sub);
			foreach ($crumbs as $key => $value) {
				$path = isset($path) ? $path . '/' . $value : $parent . '/' . $value;
				if (!file_exists($path)) {
					@mkdir($path);
				}
			}

			return $path;
		} else {
			@mkdir($parent . '/' . $sub);
			return $parent . '/' . $sub;
		}
	}
}

function getExtension($filename) {
	return pathinfo($filename, PATHINFO_EXTENSION);
}

	
function stringBetween($string, $start, $end){
  $string = ' ' . $string;
  $ini = strpos($string, $start);
  if ($ini == 0) return '';
  $ini += strlen($start);
  $len = strpos($string, $end, $ini) - $ini;
  return substr($string, $ini, $len);
}

function randomString($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

function sendJsonResponse($array, $exit = true) {
	echo json_encode($array);
	exit;
}

function buildPagination($currentPage, $limit, $total) {
	$pagination = array();
	$totalPages = ($total / $limit);
	if (is_float($totalPages)) {
		$rounded = round($totalPages);
		if ($rounded < $totalPages) {
			$totalPages = ($rounded + 1);
		} else {
			$totalPages = $rounded;
		}
	}

	$pagination['first'] = array('page' => 1, 'url' => buildPaginationUrl(1));
	$pagination['last'] = array('page' => $totalPages, 'url' => buildPaginationUrl($totalPages));
	$pagination['current'] = array('page' => $currentPage, 'url' => false);

	if ($currentPage > 3) {
		$pagination['pages'][] = array('page' => ($pageNow = $currentPage - 3), 'url' => buildPaginationUrl($pageNow));
	}

	if ($currentPage > 2) {
		$pagination['pages'][] = array('page' => ($pageNow = $currentPage - 2), 'url' => buildPaginationUrl($pageNow));
	}

	if ($currentPage > 1) {
		$pagination['pages'][] = array('page' => ($pageNow = $currentPage - 1), 'url' => buildPaginationUrl($pageNow));
	}

	$pagination['pages'][] = array('page' => $currentPage);
	if ($currentPage < $totalPages) {
		if ($currentPage + 1 <= $totalPages) {
			$pagination['pages'][] = array('page' => $pageNow = $currentPage + 1, 'url' => buildPaginationUrl($pageNow));
		}

		if ($currentPage + 2 <= $totalPages) {
			$pagination['pages'][] = array('page' => ($pageNow = $currentPage + 2), 'durl' => buildPaginationUrl($currentPage + 2));
		}

		if ($currentPage + 3 <= $totalPages) {
			$pagination['pages'][] = array('page' => ($pageNow = $currentPage + 3), 'url' => buildPaginationUrl($pageNow));
		}
	}

	return $pagination;
}

function directory($directory) {
	return str_replace('-', '/', substr($directory, 0, 10));
}

function listFiles($path, $parentNames = false, $depth = false, $deep = false) {
	$files = array();
	$listed = glob("$path/*");
	foreach ($listed as $key => $path) {
		if (is_dir($path) && $depth && $depth > $deep) {
			$files = array_merge($files, listFiles($path, $depth, $deep + 1));
		} else {
			$filename = $parentNames ? basename(dirname($path)) . '/' . basename($path) : basename($path);
			$files[$filename] = $path;
		}
	}

	return $files;
}

function getMessage($name) {
	$file = HELPERS_DIRECTORY . "/notifications/{$name}.json";
	if (file_exists($file)) {
		return json_decode(file_get_contents($file), true);
	}
}

function prepareMessage($message, $fields = false) {
	// Following are default fields, if a field is not matched here
	// only then we'll search it in fields section
	// websitename :: 
	// websitelink ::
	// date ::
	global $limbs;
	$websiteName = $limbs->settings->get('title');
	$websiteLink = $limbs->settings->get('core_url');
	$currentDate = date("Y/m/d");

	$message = str_replace('{websitename}', $websiteName, $message);
	$message = str_replace('{websitelink}', $websiteLink, $message);
	$message = str_replace('{date}', $currentDate, $message);
	if ($fields) {
		foreach ($fields as $key => $value) {
			if (strstr($message, '{' . $key . '}')) {
				$message = str_replace('{' . $key . '}', $value, $message);
			}
		}
	}

	return $message;
}

function buildUrl($parameters, $append = array(), $skip = array()) {
	$skip = array_merge($skip, array('activate', 'deactivate', 'delete', 'main', 'section', 'crumbs'));
	$parameters = array_merge($parameters, $append);
	$url = CORE_URL . '/' . $parameters['main'];
	if (isset($parameters['section'])) {
		$url .= '/' . $parameters['section'];
	}

	foreach ($parameters as $key => $value) {
		if (!in_array($key, $skip) && !empty($value)) {
			$url .= "/$key/$value";
		}
	}

	return $url;
}

function buildPaginationUrl($page) {
	$_GET['page'] = $page;
	return buildUrl($_GET);
}

function hook($function, $parameters = false) {
	if (function_exists($function)) {
		return 'FUCK';
	}
}

function hookable($function) {
	return function_exists($function) ? true : false;
}