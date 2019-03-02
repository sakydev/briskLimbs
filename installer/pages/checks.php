<?php

$requirments = json_decode(file_get_contents(INSTALLER_CORE . '/requirments.json'), true);
$response = array();

$phpVersion = getPhpVersion();
$status = $phpVersion >= $requirments['php'] ? 'Ok' : $requirments['php'] . ' or higher required';

$mysqlVersion = getMysqlVersion();
$status = $phpVersion >= $requirments['mysql'] ? 'Ok' : $requirments['mysql'] . ' or higher required';

$shell = hasShellAccess() ? 'Ok' : 'Disabled';
$ffmpeg = hasFfmpeg();
$ffmpegStatus = $ffmpeg ? 'Ok' : 'Not found';

$ffprobe = hasFfprobe();
$ffprobeStatus = $ffprobe ? 'Ok' : 'Not found';

$response['php'] = array(
  'version' => $phpVersion,
  'status' => $status
);

$response['mysql'] = array(
  'version' => $mysqlVersion,
  'status' => $status
);

$response['shell_exec'] = array(
  'version' => '',
  'status' => $shell
);

$response['ffmpeg'] = array(
  'version' => $ffmpeg,
  'status' => $ffmpegStatus
);

$response['ffprobe'] = array(
  'version' => $ffprobe,
  'status' => $ffprobeStatus
);

?>

<body class="text-center">
  <div class="form-signin" action="install.php?section=checks">
    <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
    <h1 class="h3 mb-3 font-weight-normal">Requirment Checks</h1>
    <ul class="list-group">
      <?php
        foreach ($response as $item => $value) {
          $item = ucfirst($item);
          $version = $value['version'] ? ' (' . $value['version'] .')' : '';
          $status = $value['status'];
          $class = $status == 'Ok' ? 'success' : 'warning';
          echo "<li class='list-group-item'>{$item}{$version}: <span class='badge badge-{$class}'>{$status}</span></li>";
        } 
      ?>
    </ul>
    <a href="install.php?section=import"><button class="btn btn-primary mt-2">Proceed</button></a>

  </div>
</body>