<?php

global $limbs, $users;

if (!$users->isAdmin()) {
	jumpTo('home');
}

$videos = new Videos();
$conversions = $registrations = $uploads = $listParameters = array();
$yesterdayDate = date('Y-m-d', strtotime('-1 days'));
$listParameters['date'] = array("$yesterdayDate  00:00::00", "$yesterdayDate 23:59::59", 'between');
$uploads['yesterday'] = $videos->count($listParameters);
$conversions['yesterday'] = json_encode($limbs->database->rawQuery("SELECT status AS label, COUNT(*) AS y FROM videos WHERE date BETWEEN '$yesterdayDate  00:00::00' AND '$yesterdayDate 23:59::59' GROUP BY status"));

$todayDate = date('Y-m-d');
$listParameters['date'] = array("$todayDate  00:00::00", "$todayDate 23:59::59", 'between');
$uploads['today'] = $videos->count($listParameters);
$conversions['today'] = json_encode($limbs->database->rawQuery("SELECT status AS label, COUNT(*) AS y FROM videos WHERE date BETWEEN '$todayDate  00:00::00' AND '$todayDate 23:59::59' GROUP BY status"));

$weekStart = date('Y-m-d', strtotime('-7 days'));
$weekEnd = date('Y-m-d', time());
$uploads['last_week'] = json_encode($limbs->database->rawQuery("SELECT DATE(date) AS label,COUNT(*) AS y FROM videos WHERE date BETWEEN '$weekStart' AND '$weekEnd' GROUP BY DATE(date)"));
$conversions['last_week'] = json_encode($limbs->database->rawQuery("SELECT status AS label, COUNT(*) AS y FROM videos WHERE date BETWEEN '$weekStart' AND '$weekEnd' GROUP BY status"));

$weekEnd = $weekStart; // end where last week starts
$weekStart = date('Y-m-d', strtotime('-14 days'));
$uploads['before_last_week'] = json_encode($limbs->database->rawQuery("SELECT DATE(date) AS label,COUNT(*) AS y FROM videos WHERE date BETWEEN '$weekStart' AND '$weekEnd' GROUP BY DATE(date)"));
$conversions['before_last_week'] = json_encode($limbs->database->rawQuery("SELECT status AS label, COUNT(*) AS y FROM videos WHERE date BETWEEN '$weekStart' AND '$weekEnd' GROUP BY status"));

$monthStart = date('Y-m-d', strtotime('-30 days'));
$monthEnd = date('Y-m-d', time());

$uploads['last_month'] = json_encode($limbs->database->rawQuery("SELECT DATE(date) AS label,COUNT(*) AS y FROM videos WHERE date BETWEEN '$monthStart' AND '$monthEnd' GROUP BY DATE(date)"));
$conversions['last_month'] = json_encode($limbs->database->rawQuery("SELECT status AS label, COUNT(*) AS y FROM videos WHERE date BETWEEN '$monthStart' AND '$monthEnd' GROUP BY status"));

$monthEnd = $monthStart; // end where last week starts
$monthStart = date('Y-m-d', strtotime('-60 days'));
$uploads['before_last_month'] = json_encode($limbs->database->rawQuery("SELECT DATE(date) AS label,COUNT(*) AS y FROM videos WHERE date BETWEEN '$monthStart' AND '$monthEnd' GROUP BY DATE(date)"));
$conversions['before_last_month'] = json_encode($limbs->database->rawQuery("SELECT status AS label, COUNT(*) AS y FROM videos WHERE date BETWEEN '$monthStart' AND '$monthEnd' GROUP BY status"));

$news = json_decode(file_get_contents('http://brisklimbs.com/adminsApi/news'), true);

$stats['uploads'] = $uploads;
$stats['conversions'] = $conversions;
$parameters['news'] = $news;
$parameters['mainSection'] = 'dashboard';
$parameters['_errors'] = $limbs->errors->collect();
$parameters['_title'] = 'Admin Dashboard';
$parameters['stats'] = $stats;

$limbs->display('dashboard.html', $parameters);