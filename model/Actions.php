<?php

/*
* Actions of all kinds like watching a video, subscribe to use etc
*/
class Actions {
  
  function __construct() {
    $this->initialize();
  }

  public function initialize() {
    global $database, $users, $videos;
    $this->database = $database;
    $this->users = $users;

    if (!$videos) {
      $videos = new Videos();
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