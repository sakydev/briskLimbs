<?php

  /**
  *
  */

class Thumbnails {
  
  function __construct($filename, $directory, $url = false) {
    $this->url = $url;
    $this->filename = $filename;
    $this->directory = THUMBNAILS_DIRECTORY . '/' . $directory . '/' . $filename;
  }

  public function getDefault() {
    return MEDIA_URL . '/defaults/thumbnail.png';
  }
  
    public function get($path = false) {
    $results = $path ? glob($this->directory . "*") : glob($this->directory . "*");
    if ($this->url) {
      foreach ($results as $key => $path) {
        $results[$key] = str_replace(CORE_DIRECTORY, CORE_URL, $path);
      }
    }

    return $results;
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
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_highest')) {
        return $path;
      }
    }
  }

  public function lowest() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_lowest')) {
        return $path;
      }
    }
  }

  public function medium() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_medium')) {
        return $path;
      }
    }
  }

  public function original() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_original')) {
        return $path;
      }
    }
  }

  public function small() {
    foreach ($this->get() as $key => $path) {
      if (strstr(basename($path), '_small')) {
        return $path;
      }
    }
  }

  public function time() {
    return filemtime($this->lowest());
  }

  public function delete() {
    if ($results = $this->get()) {
      foreach ($results as $key => $path) {
        @unlink($path);
      }
    }

    return true;
  }
}