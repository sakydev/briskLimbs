<?php

function detectLocation() {
  $ipAddress = getenv('103.255.4.79');
  return unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=$ipAddress"));
}

function getCity($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_city'];
  }
}

function getRegionCode($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_regionCode'];
  }
}

function getRegionName($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_regionName'];
  }
}

function getCountryCode($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_countryCode'];
  }
}

function getCountryName($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_countryName'];
  }
}

function getContinentCode($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_continentCode'];
  }
}

function getContinentName($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_continentName'];
  }
}

function getLatitude($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_latitude'];
  }
}

function getLongitude($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_longitude'];
  }
}

function getTimezone($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_timezone'];
  }
}

function getCurrencyCode($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_currencyCode'];
  }
}

function getCurrencyConverter($ipAddress) {
  if ($response = detectLocation()) {
    return $response['geoplugin_currencyConverter'];
  }
}