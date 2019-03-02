<?php

function formatDuration($seconds) {
	if ($seconds < 60) {
		return $seconds < 10 ? "00:0{$seconds}" : "00:{$seconds}";
	}

		$rem = $seconds % 3600;
	if ($seconds > 3599) {
		$hours = ($seconds - $rem) / 3600;
		$hours = strlen($hours) < 2 ? "0{$hours}" : $hours;
		return "{$hours}:" . formatDuration($rem);
	}

	$rem = $seconds % 60;
	$minutes = ($seconds - $rem) / 60;
	$minutes = strlen($minutes) < 2 ? "0{$minutes}" : $minutes;
	$rem = strlen($rem) < 2 ? "0{$rem}" : $rem;

	return "{$minutes}:{$rem}";
}

function formatDate($date, $postfix = 'ago') {
	$descriptions = array('y' => 'year', 'm' => 'month', 'd' => 'day', 'h' => 'hour', 'm' => 'minute', 's' => 'second');
	$published = new DateTime($date);
	$dateNow = new DateTime(date("Y-m-d H:i:s"));
	$interval = $published->diff($dateNow);
	
	foreach ($descriptions as $key => $value) {
		if (!empty($interval->$key)) {
			$span = $interval->$key;
			return $span . ' ' . ($span > 1 ? $value . 's' : $value) . ' ' . $postfix;
		}
	}
}

function timeSeconds($time) {
  sscanf($time, "%d:%d:%d", $hours, $minutes, $seconds);
  return isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
}