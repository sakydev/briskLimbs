<?php

/**
* Name: Logs
* Description: A class to create helpful logs
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Logs.php
*/

class Logs {
  /*
  * Holds path of file to write to
  */
  public $path;

  function __construct($path) {
    $this->path = $path;
    $this->initialize();
  }

  /**
  * Create initial log data
  * @return: { boolean }
  */
  public function initialize() {
    return $this->write("Log started @ " . date("Y/m/d - H:i:s"));
  }

  /**
  * Check if a log file exists
  * @return: { boolean }
  */
  public function exists() {
    return file_exists($this->path);
  }

  /**
  * Write a new line to log
  * @param: { $txt } { string } { text to write }
  * @param: { $print } { boolean } { false by default, print and write log if true }
  */
  public function write($txt, $print = false) {
    if ($print) {
      displayMessage($txt);
    }

    return file_put_contents($this->path, $txt . PHP_EOL , FILE_APPEND | LOCK_EX);
  }

  /**
  * Reads a log file
  * @return: { string }
  */
  public function read() {
    return file_get_contents($this->path);
  }
}
