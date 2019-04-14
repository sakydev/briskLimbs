<?php
  
  $url = $_GET['url'];
  $adminUrl = $url . '/admin/settings';
  $success = false;

  if (isset($_POST['username'])) {
    if ($message = createAdmin($_POST['username'], $_POST['password'], $_POST['email']) == true) {
      $success = true;
    } else {
      $error = $message;
    }
  }

?>
<body class="text-center">
  <form class="form-signin" method="post" action="">
    <img class="mb-4" src="../skins/ivar/assets/images/logo.svg" alt="" width="200">
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger"><?=$error?></div>
    <?php elseif ($success): ?>
      <div class="alert alert-success">Installation successful. [ <a href="<?=$adminUrl?>">Admin Area</a> ] [ <a href="<?=$url?>">Frontend</a> ]</div>
    <?php else: ?>
      <h4>Final Step: Admin Credentials</h4>
      <input type="text" name="username" class="form-control" placeholder="Admin username here" required autofocus>
      <input type="email" name="email" class="form-control" placeholder="Admin email here" required autofocus>
      <input type="password" name="password" class="form-control" placeholder="Admin password here" required autofocus>
      <button class="btn btn-primary mt-2" type="submit" value="install">Finish</button>
    <?php endif; ?>
  </form>
</body>