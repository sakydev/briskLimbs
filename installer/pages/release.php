<?php

$release = json_decode(file_get_contents(INSTALLER_CORE . '/release.json'), true);

?>

<body class="text-center">
  <div class="form-signin" action="install.php?section=checks">
    <img class="mb-4" src="../skins/ivar/assets/images/logo.svg" alt="" width="200">
    <h1 class="h3 mb-3 font-weight-normal">Release Information</h1>
    <ul class="list-group">
      <?php
        foreach ($release as $key => $value) {
          if (!is_array($value)) {
            echo '<li class="list-group-item">' . ucfirst($key) . ' : ' . ucfirst($value) . '</li>';
          } else {
            $mainKey = $key;
            foreach ($value as $key => $subValue) {
              echo '<li class="list-group-item">' . ucfirst($mainKey . ' ' . $key) . ' : ' . ucfirst($subValue) . '</li>';
            }
          }
        } 
      ?>
    </ul>
    <a href="install.php?section=checks"><button class="btn btn-primary mt-2">Proceed</button></a>

  </div>
</body>