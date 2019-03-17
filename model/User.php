<?php

/**
* Name: User
* Description: A helpful class to interact with single user
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/User.php
*/

class User extends Users {
  
  function __construct($user) {
    $this->user = $user;
    $this->data = array();
  }

  /**
  * Fetches initial data for user and makes other methods available
  * @return: { $data } { object }
  */
  public function fetch() {
    $this->initialize();
    return $this->data = is_numeric($this->user) ? $this->getById($this->user, '*') : $this->getByUsername($this->user, '*');
  }

  /**
  * Get username of user
  * @return: { string }
  */
  public function name() {
    return $this->data['username'];
  }

  /**
  * Get userid of user
  * @return: { integer }
  */
  public function userId() {
    return $this->data['id'];
  }

  /**
  * Get email of user
  * @return: { string }
  */
  public function email() {
    return $this->data['email'];
  }

  /**
  * Get level of user
  * @return: { string }
  */
  public function level() {
    return $this->data['level'];
  }

  /**
  * Get signup date of user
  * @return: { integer }
  */
  public function date() {
    return $this->data['date'];
  }

  /**
  * Get status of user
  * @return: { string }
  */
  public function status() {
    return $this->data['status'];
  }

  /**
  * Get date of birth of user
  * @return: { string }
  */
  public function dob() {
    return $this->data['dob'];
  }

  /**
  * Get bio of user
  * @return: { string }
  */
  public function bio() {
    return $this->data['bio'];
  }

}