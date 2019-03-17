<?php

/**
* Name: Thumbnails
* Description: Core class for handling related actions
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Thumbnails.php
*/

class Thumbnails {
  /*
  * Holds filename for which fetching thumbnails
  */
  public $filename;

  /*
  * Holds directory to search thumbnails in
  */
  public $directory;

  /*
  * Holds if URL or directory should be returned
  */
  public $url;

  function __construct($filename, $directory, $url = false) {
    $this->url = $url;
    $this->filename = $filename;
    $this->directory = THUMBNAILS_DIRECTORY . '/' . $directory . '/' . $filename;
  }

  /**
  * Get default thumbnail URL
  * @return: { string }
  */
  public function getDefault() {
    return MEDIA_URL . '/defaults/thumbnail.png';
  }
  
  /**
  * Get all thumbnails for given video
  * @param: { $path } { string } { false by default, direct path to search in }
  * @return: { array }
  */ 
  public function get($path = false) {
    $results = $path ? glob($this->directory . "*") : glob($this->directory . "*");
    if ($this->url) {
      foreach ($results as $key => $path) {
        $results[$key] = str_replace(CORE_DIRECTORY, CORE_URL, $path);
      }
    }

    return $results;
  }

  /**
  * Count total thumbnails found for a video
  * @return: { integer }
  */
  public function count() {
    return count($this->get());
  }

  /**
  * Get total size of found thumbnails
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
  * Return first thumbnail
  * @return: { string }
  */
  public function first() {
    if ($results = $this->get()) {
      return current($results);
    }
  } 

  /**
  * Return last thumbnail
  * @return: { string }
  */
  public function last() {
    if ($results = $this->get()) {
      return end($results);
    }
  }

  /**
  * Return highest quality thumbnail
  * @return: { string }
  */
  public function highest() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_highest')) {
        return $path;
      }
    }
  }

  /**
  * Return lowest quality thumbnail
  * @return: { string }
  */
  public function lowest() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_lowest')) {
        return $path;
      }
    }
  }

  /**
  * Return media thumbnail
  * @return: { string }
  */
  public function medium() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_medium')) {
        return $path;
      }
    }
  }

  /**
  * Return original thumbnail if it exists
  * @return: { string }
  */
  public function original() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_original')) {
        return $path;
      }
    }
  }

  /**
  * Return smallest thumbnail
  * @return: { string }
  */
  public function small() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_small')) {
        return $path;
      }
    }
  }

  /**
  * Return last modified time of thumbnails
  * @return: { integer }
  */
  public function time() {
    return filemtime($this->lowest());
  }

  /**
  * Delete all thumbnails for a video
  * @return: { boolean }
  */
  public function delete() {
    if ($results = $this->get()) {
      foreach ($results as $key => $path) {
        @unlink($path);
      }
    }

    return true;
  }
}