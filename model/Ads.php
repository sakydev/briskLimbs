<?php

/**
* Name: Ads
* Description: Core class for handling Ads related actions
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Ads.php
*/

class Ads {
  
  function __construct() {
    global $limbs;
    $this->limbs = $limbs;
    $this->database = $limbs->database;
    $this->table = 'ads';
    $this->KEYS = $limbs->database->getColumnsList($this->table);
    $this->errors = new Errors();
    $this->defaultLimit = 10;
  }

  public function count($parameters = false) {
    if ($parameters) {
      $parameters['count'] = true;
      return $this->list($parameters);
    } else {
      return $this->database->getValue($this->table, 'count(*)');
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
    $sort = isset($parameters['sort']) ? $parameters['sort'] : false;
    if ($sort) {
      if (is_array($sort)) {
        $this->database->orderBy($sort['0'], isset($sort['1']) ? $sort['1'] : 'DESC');
      } else {
        $this->database->orderBy($sort);
      }
    }

    return isset($parameters['count']) ? $this->database->getValue($this->table, 'count(*)') : $this->database->get($this->table, $limit);
  }

  public function exists($name) {
    $this->database->where('name', $name);
    return $this->database->getValue($this->table, 'count(*)');
  }

  public function locationOccupied($name, $location) {
    $this->database->where('name', $name);
    $this->database->where('location', $location);
    return $this->database->getValue($this->table, 'count(*)'); 
  }

  public function active($parameters) {
    $parameters['status'] = 'active';
    return $this->list($parameters);
  }

  public function inactive($limit) {
    $parameters['status'] = 'inactive';
    return $this->list($parameters);
  }

  public function getField($name, $field) {
    $this->database->where('name', $name);
    $results = $this->database->get($this->table, null, array($field));
    return isset($results['0'][$field]) ? $results['0'][$field] : false;
  }

  public function getFields($name, $fields) {
    $this->database->where('name', $name);
    $results = $this->database->get($this->table, null, is_array($fields) ? $fields : array($fields));
    return isset($results['0']) ? $results['0'] : false;
  }

  public function getByName($name) {
    $this->database->where('status', 'active');
    $this->database->where('name', $name);
    return $this->database->getOne($this->table);
  }

  public function getByLocation($location) {
    $this->database->where('status', 'active');
    $this->database->where('location', $location);
    return $this->database->getOne($this->table);
  }

  // set('status', 'successful', 'sad2314', 'vkey');
  public function set($field, $value, $identifierValue, $identifier = 'name') {
    $this->database->where($identifier, $identifierValue);
    return $this->database->update($this->table, array($field => $value));
  }

  // update a single column of multiple videos
  public function bulkSet($field, $value, $identifierValueArray, $identifier = 'name') {
    $this->database->where($identifier, $identifierValueArray, 'IN');
    return $this->database->update($this->table, array($field => $value));
  }
  
  public function activate($video) {
    return $this->set('status', 'active', $video, is_numeric($video) ? 'id' : 'name');
  }

  public function bulkActivate($videosArray, $identifier = 'username') {
    return $this->bulkSet('status', 'active', $videosArray, $identifier);
  }

  public function deactivate($video) {
    return $this->set('status', 'inactive', $video, is_numeric($video) ? 'id' : 'name');
  }

  public function bulkDeactivate($videosArray, $identifier = 'name') {
    return $this->bulkSet('status', 'inactive', $videosArray, $identifier);
  }

  public function delete($name) {
    $this->database->where('name', $name);
    return $this->database->delete($this->table);
  }

  private function validate($fields, $update = false) {
    $required = array('name', 'location', 'content');

    foreach ($fields as $key => $value) {
      if (!in_array($key, $this->KEYS)) {
        return $this->limbs->errors->add("Invalid field specified ($key)");
      }

      if (empty($value) && in_array($key, $required)) {
        return $this->limbs->errors->add("Field ($key) is required"); 
      }
    }

    if ($this->exists($fields['name'])) {
      return $this->limbs->errors->add("Another ad with same name already exists");  
    }

    if ($this->locationOccupied($fields['name'], $fields['location'])) {
      return $this->limbs->errors->add("Another ad with same location already exists");   
    }

    return true;
  }

  public function create($parameters) {
    if ($this->validate($parameters)) {
      return $this->database->insert($this->table, $parameters);
    }
  }
}