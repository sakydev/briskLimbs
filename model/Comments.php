<?php

class Comments {
  
  function __construct() {
    
  }

  function initialize() {
    global $limbs, $users, $videos;
    $this->limbs = $limbs;
    $this->users = $users;
    $this->videos = $videos;
    $this->database = $this->limbs->database;
    $this->table = 'comments';
    $this->KEYS = $this->database->getColumnsList($this->table);
    $this->defaultLimit = 10;
  }

  public function get($id) {

  }

  public function set($id) {

  }

  public function validate($comment, $video) {
    if (!$this->users->authenticated()) {
      return $this->limbs->errors->add('You must be logged in to comment');
    }
    return true; // more validations to be added later
  }

  public function add($comment, $video) {
    if ($this->validate($comment, $video)) {
      $params = array();
      $params['vkey'] = $video;
      $params['userid'] = $this->users->userId();
      $params['username'] = $this->users->username();
      $params['comment'] = $comment;
      if ($this->database->insert($this->table, $params)) {
        $this->videos->setComments($video);
        return true;
      }
    }
  }

  public function list($parameters = false) {
    if (is_array($parameters)) {
      foreach ($parameters as $column => $condition) {
        if (in_array($column, $this->KEYS)) {
          if (is_array($condition)) {
            $this->database->where($column, $condition['0'], $condition['1']);
          } else {
            $this->database->where($column, $condition);
          }
        }
      }
    }

    $limit = isset($parameters['limit']) ? $parameters['limit'] : $this->defaultLimit;
    $sort = isset($parameters['sort']) ? $parameters['sort'] : 'id';
    if ($sort) {
      if (is_array($sort)) {
        $this->database->orderBy($sort['0'], isset($sort['1']) ? $sort['1'] : 'DESC');
      } else {
        $this->database->orderBy($sort);
      }
    }
    
    return isset($parameters['count']) ? $this->database->getValue($this->table, 'count(*)') : $this->database->get($this->table, $limit);
  }
}