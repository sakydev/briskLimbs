<?php

if (isset($_POST['username'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $database = $_POST['database'];

  $status = import($username, $password, $database);
  if (!is_array($status)) {
    $message = $status;
  } else {
    $url = $status['url'];
    $data = "<?php \$DATABASE_CONFIGS = array('host' => 'localhost', 'username' => '$username', 'password' => '$password', 'database' => '$database'); ?>";
    file_put_contents(CORE_DIRECTORY . '/configs/db.php', $data);
    header("Location: install.php?section=finish&url={$url}");
    exit;
  }
}

?>

<body class="text-center">
  <form class="form-signin" method="post" action="">
    <img class="mb-4" src="https://getbootstrap.com/docs/4.0/assets/brand/bootstrap-solid.svg" alt="" width="72" height="72">
    <?php if (!empty($message)): ?>
      <div class="alert alert-danger"><?=$message?></div>
    <?php endif; ?>
    <h1 class="h3 mb-3 font-weight-normal">Database Details</h1>
    <label for="username" class="float-left mt-2">Database Username</label>
    <input type="user" name="username" class="form-control" placeholder="Database username here" required autofocus>
    <label for="password" class="float-left mt-2">Database Password</label>
    <input type="password" name="password" class="form-control" placeholder="Database password here" required>
    <label for="database" class="float-left mt-2">Database Name</label>
    <input type="database" name="database" class="form-control" placeholder="Database name here" required>
    <button class="btn btn-primary mt-2" type="submit" value="install">Install</button>
  </form>
</body>