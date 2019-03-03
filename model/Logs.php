<?php

class Logs {
  
  function __construct($path) {
    $this->path = $path;
  }

  public function initialize() {
    return $this->write("Log started @ " . date("Y/m/d - H:i:s"));
  }

  public function exists() {
    return file_exists($this->path);
  }

  public function write($txt, $print = false) {
    if ($print) {
      displayMessage($txt);
    }

    return file_put_contents($this->path, $txt . PHP_EOL , FILE_APPEND | LOCK_EX);
  }

  public function read() {
    return file_get_contents($this->path);
  }
}
