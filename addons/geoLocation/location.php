<?php

require 'functions.php';
$addons = new Addons();

$addons->addHook('detectLocation', 'detectLocation');
$addons->addHook('getCity', 'getCity');
$addons->addHook('getRegionCode', 'getRegionCode');
$addons->addHook('getRegionName', 'getRegionName');
$addons->addHook('getCountryCode', 'getCountryCode');
$addons->addHook('getCountryName', 'getCountryName');
$addons->addHook('getContinentCode', 'getContinentCode');
$addons->addHook('getContinentName', 'getContinentName');
$addons->addHook('getLatitude', 'getLatitude');
$addons->addHook('getLongitude', 'getLongitude');
$addons->addHook('getTimezone', 'getTimezone');
$addons->addHook('getCurrencyCode', 'getCurrencyCode');
$addons->addHook('getCurrencyConverter', 'getCurrencyConverter');