<?php

/**
 * 
 */
class User extends Users {
  
  function __construct($user) {
    $this->user = $user;
    $this->data = array();
  }

  public function fetch() {
    $this->initialize();
    return $this->data = is_numeric($this->user) ? $this->getById($this->user, '*') : $this->getByUsername($this->user, '*');
  }

  public function name() {
    return $this->data['username'];
  }

  public function userId() {
    return $this->data['id'];
  }

  public function email() {
    return $this->data['email'];
  }

  public function level() {
    return $this->data['level'];
  }

  public function date() {
    return $this->data['date'];
  }

  public function status() {
    return $this->data['status'];
  }

  public function dob() {
    return $this->data['dob'];
  }

  public function bio() {
    return $this->data['bio'];
  }

}