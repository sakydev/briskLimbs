<?php

function pr($a) { echo '<pre>'; print_r($a); echo '</pre>'; }
function pex($a) { pr($a); exit('Pex'); }

function getPhpVersion() {
  return substr(phpversion(), 0, 3);
}

function getMysqlVersion() {
  $output = shell_exec('mysql -V'); 
  preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version); 
  return $version[0];
}

function hasShellAccess() {
  return is_callable('shell_exec') && false === stripos(ini_get('disable_functions'), 'shell_exec');
}

function hasFfmpeg() {
  return shell_exec('which ffmpeg');
}

function hasFfprobe() {
  return shell_exec('which ffprobe');
}

function import($username, $password, $database, $prefix = false) {
  $connection = new mysqli('localhost', $username, $password, $database);
  if ($connection->connect_error) {
    return $connection->connect_error;
  }

  $files = glob(INSTALLER_CORE . '/imports/*.sql');
  foreach ($files as $key => $file) {
    if ($handle = fopen($file, "r")) {
      $query = '';
      while (($line = fgets($handle)) !== false) {
        $first = substr($line, 0, 4);
        if (strstr($first, '/*') || strstr($first, '--') || strlen($line) < 5) { continue; }
        $query .= $line;
        if (substr(trim($line), -1) == ';') {
          if ($connection->query($query) != true) {
            return $connection->error;
          }
          $query = '';
        }
      }

      fclose($handle);
    } else {
      // error opening the file.
    } 
  }

  $coreUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
  $coreUrl = substr($coreUrl, 0, strpos($coreUrl, '/installer'));
  $coreDirectory = CORE_DIRECTORY;
  $query = "UPDATE settings SET value= CASE WHEN name='core_url' THEN '$coreUrl' WHEN name='core_directory' THEN '$coreDirectory' END WHERE name IN ('core_url', 'core_directory');";
  if ($connection->query($query)) {
    return array('url' => $coreUrl, 'directory' => $coreDirectory);
  } else {
    return  $connection->error;
  }
}

function createAdmin($username, $password, $email) {
  require CORE_DIRECTORY . '/configs/db.php';

  $password = md5(md5(md5($password)));
  $connection = new mysqli('localhost', $DATABASE_CONFIGS['username'], $DATABASE_CONFIGS['password'], $DATABASE_CONFIGS['database']);

  if ($connection->connect_error) {
    return $connection->connect_error;
  }

  $query = "INSERT INTO users (username, password, email, level, status) VALUES ('$username', '$password', '$email', 1, 'ok')";
  if ($connection->query($query)) {
    return true;
  } else {
    return $connection->error;
  }
}