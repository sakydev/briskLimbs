<?php

  /**
  *
  */

class Files {
  
  function __construct($filename, $directory, $url = false) {
    $this->url = $url;
    $this->filename = $filename;
    $this->directory = VIDEOS_DIRECTORY . '/' . $directory . '/' . $filename;
  }

  public function get() {
    $results = glob($this->directory . "*");
    $qualities = array();
    foreach ($results as $key => $path) {
      $qualities[stringBetween($path, '-', '.')] = $this->url ? str_replace(CORE_DIRECTORY, CORE_URL, $path) : $path;
    }

    return $qualities;
  }

  public function count() {
    return count($this->get());
  }

  public function size() {
    $size = 0;
    foreach ($this->get() as $key => $path) {
      $size += filesize($path);
    }

    return $size;
  }

  public function first() {
    if ($results = $this->get()) {
      return current($results);
    }
  } 

  public function last() {
    if ($results = $this->get()) {
      return end($results);
    }
  }

  public function highest() {
    $results = $this->get();
    return $results[max(array_keys($results))];
  }

  public function lowest() {
    $results = $this->get();
    return $results[min(array_keys($results))];
  }

  public function time() {
    return filemtime($this->lowest());
  }

  public function delete() {
    if ($results = $this->get()) {
      foreach ($results as $key => $path) {
        unlink($path);
      }
    }

    return true;
  }
}