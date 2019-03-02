<?php

/*
* Actions of all kinds like watching a video, subscribe to use etc
*/
class Actions {
  
  function __construct() {
    global $database, $users;
    $this->database = $database;
    $this->users = $users;
  }

  public function initialize() {
    global $videos;
    if (!$videos) {
      $videos = new Videos();
      $videos->initialize();
    }

    $this->videos = $videos;
  }

  public function watched($video) {
    $cookie = isset($_COOKIE['watch_' . $video]) ? true : false;
    if (!$cookie) {
      setcookie('watch_' . $video, true, time() + (86400 * 30), "/");
      return $this->videos->setViews($video);
    }
  }
}