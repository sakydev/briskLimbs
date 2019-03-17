<?php

/**
* Name: Files
* Description: Core class for handling files related actions
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Files.php
*/

class Files {
  /*
  * Holds filename for which fetching files
  */
  public $filename;

  /*
  * Holds directory to search files in
  */
  public $directory;

  /*
  * Holds if URL or directory should be returned
  */
  public $url;

  function __construct($filename, $directory, $url = false) {
    $this->url = $url;
    $this->filename = $filename;
    $this->directory = VIDEOS_DIRECTORY . '/' . $directory . '/' . $filename;
  }

  /**
  * Get default file URL
  * @return: { string }
  */
  public function getDefault() {
    return $this->get(MEDIA_DIRECTORY . '/defaults/videos/*');
  }

  /**
  * Get all files for given video
  * @param: { $path } { string } { false by default, direct path to search in }
  * @return: { array }
  */ 
  public function get($path = false) {
    $results = $path ? glob($path) : glob($this->directory . "*");
    $qualities = array();
    foreach ($results as $key => $path) {
      $qualities[stringBetween($path, '-', '.')] = $this->url ? str_replace(CORE_DIRECTORY, CORE_URL, $path) : $path;
    }

    return $qualities;
  }

  /**
  * Count total files found for a video
  * @return: { integer }
  */
  public function count() {
    return count($this->get());
  }

  /**
  * Get total size of found files
  * @return: { integer }
  */
  public function size() {
    $size = 0;
    foreach ($this->get() as $key => $path) {
      $size += filesize($path);
    }

    return $size;
  }

  /**
  * Return first file
  * @return: { string }
  */
  public function first() {
    if ($results = $this->get()) {
      return current($results);
    }
  } 

  /**
  * Return last file
  * @return: { string }
  */
  public function last() {
    if ($results = $this->get()) {
      return end($results);
    }
  }

  /**
  * Return highest quality file
  * @return: { string }
  */
  public function highest() {
    $results = $this->get();
    return $results[max(array_keys($results))];
  }

  /**
  * Return lowest quality file
  * @return: { string }
  */
  public function lowest() {
    $results = $this->get();
    return $results[min(array_keys($results))];
  }

  /**
  * Return last modified time of files
  * @return: { integer }
  */
  public function time() {
    return filemtime($this->lowest());
  }

  /**
  * Delete all files for a video
  * @return: { boolean }
  */
  public function delete() {
    if ($results = $this->get()) {
      foreach ($results as $key => $path) {
        unlink($path);
      }
    }

    return true;
  }
}