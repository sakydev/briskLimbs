<?php

/**
* Name: Categories
* Description: Core class for handling categories CRUD and other actions
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Categories.php
*/

class Categories {
  /*
  * Holds global Limbs object
  */
  protected $limbs;

  /*
  * Holds global Users object
  */
  protected $users;

  /*
  * Holds global Database object
  */
  protected $database;

  /*
  * Holds active table for Categories class
  */
  protected $table;

  /*
  * Holds list of columns for active table
  */
  private $KEYS;

  /*
  * Holds default listing limit for class
  */
  private $defaultLimit;

  function __construct() {
    $this->initialize();
  }

  public function initialize() {
    global $database, $limbs, $users;
    $this->limbs = $limbs;
    $this->users = $users;
    $this->database = $database ? $database : $this->limbs->database;
    $this->table = 'categories';
    $this->KEYS = $this->database->getColumnsList($this->table);
    $this->defaultLimit = 10;
  }

  /**
  * Detect if a given value is category id or category name
  * @param: { $identifier } { category id or category name }
  * @return: { string / integer } { id or name }
  */
  private function column($identifier) {
    return is_numeric($identifier) ? 'id' : 'name';
  }

  /**
  * Check if a value exists against given columns
  * @param: { $fields } { string / array } { single field or set of field => value to match }
  * @param: { $value } { mixed } { false by default, value to match against when $fields is string }
  * @param: { $fetch } { string } { false by default, fields to return if match is found }
  * @return: { boolean / $fetch }
  */
  public function exists($fields, $value = false, $fetch = false) {
    if (is_array($fields)) {
      foreach ($fields as $key => $value) {
        $this->database->where($key, $value);
      }
    } else {
      $this->database->where($fields, $value);
    }

    return $fetch ? $this->database->get($this->table, null, $fetch) : $this->database->getValue($this->table, 'count(*)');
  }

  /**
  * Check if given id exists
  * @param: { $id } { integer } { id to check }
  * @return: { boolean }
  */
  public function idExists($id) {
    return $this->exists('id', $id);
  }   

  /**
  * Check if given name exists
  * @param: { $key } { string } { name to check }
  * @return: { boolean }
  */
  public function name($key) {
    return $this->exists('name', $key);
  }

  /** 
  * Get a single category
  * @param: { $identifier } { string / integer } { value to search by }
  * @param: { $type } { string } { column to search against, auto detect when false }
  * @return: { array } { results array when matched }
  */
  public function get($identifier, $type = false) {
    $this->database->where($type ? $type : $this->column($identifier), $identifier);
    return $this->database->getOne($this->table); // getOne means limit 1
  }

  /**
  * Get a single field for a single category
  * @param: { $identifier } { string / integer } { value to search } 
  * @param: { $field } { string } { field to fetch }
  * @return: { mixed } 
  */
  public function getField($identifier, $field) {
    $this->database->where($this->column($identifier), $identifier);
    $results = $this->database->get($this->table, null, array($field));
    return isset($results['0'][$field]) ? $results['0'][$field] : false;
  }

  /**
  * Get a multiple fields for a single category
  * @param: { $identifier } { string / integer } { value to search } 
  * @param: { $fields } { array } { fields to fetch }
  * @return: { mixed } 
  */
  public function getFields($identifier, $fields) {
    $this->database->where($this->column($identifier), $identifier);
    $results = $this->database->get($this->table, null, is_array($fields) ? $fields : array($fields));
    return isset($results['0']) ? $results['0'] : false;
  }

  /**
  * Update a single field of single category
  * @param: { $field } { string } { field to update }
  * @param: { $value } { mixed } { new value to set }
  * @param: { $identiferValue } { mixed } { value to search category by }
  * @param: { $identifier } { string } { name by default, column to search against }
  * @return: { boolean }
  */
  public function setField($field, $value, $identifierValue, $identifier = 'name') {
    $this->database->where($identifier, $identifierValue);
    return $this->database->update($this->table, array($field => $value));
  }

  /**
  * update a single field of multiple categories
  * @param: { $field } { string } { field to update }
  * @param: { $value } { mixed } { new value to set }
  * @param: { $identifierValueArray } { array } { values array to search categories by }
  * @param: { $identifier } { string } { name by default, column to search against }
  * @return: { boolean }
  */
  public function setFieldBulk($field, $value, $identifierValueArray, $identifier = 'name') {
    $this->database->where($identifier, $identifierValueArray, 'IN');
    return $this->database->update($this->table, array($field => $value));
  }

  /**
  * update multiple fields of single category
  * @param: { $fieldValueArray } { array } { field => value array to update }
  * @param: { $identiferValue } { mixed } { value to search category by }
  * @param: { $identifier } { string } { name by default, column to search against }
  * @return: { boolean }
  */
  public function setFields($fieldValueArray, $identifierValue, $identifier = 'name') {
    $this->database->where($identifier, $identifierValue);
    return $this->database->update($this->table, $fieldValueArray);
  }

  /**
  * update multiple columns of multiple categories
  * @param: { $fieldValueArray } { array } { field => value array to update }
  * @param: { $identifierValueArray } { array } { values array to search categories by }
  * @param: { $identifier } { string } { name by default, column to search against }
  * @return: { boolean }
  */
  public function setFieldsBulk($fieldValueArray, $identifierValueArray, $identifier = 'name') {
    $this->database->where($identifier, $identifierValueArray, 'IN');
    return $this->database->update($this->table, $fieldValueArray);
  }

  /**
  * Get all fields of category by id
  * @param: { $id } { integer } { id of category }
  * @return: { array }
  */
  public function getById($id) {
    return $this->get($id, 'id');
  }

  /**
  * Get all fields of name by key
  * @param: { $key } { integer } { key of name }
  * @return: { array }
  */
  public function getByName($name) {
    return $this->get($name, 'name');
  }

  /**
  * Get ID of name
  * @param: { $identifier } { value to search by }
  * @return: { array }
  */
  public function getId($identifier) {
    return $this->getField($identifier, 'id');
  }

  /**
  * Get name of category
  * @param: { $identifier } { value to search by }
  * @return: { array }
  */
  public function getName($identifier) {
    return $this->getField($identifier, 'name');
  }

  public function getNames($identifierArray) {
    $this->database->where('id', $identifierArray, 'IN');
    return $this->database->get($this->table);
  }

  /**
  * Get description of category
  * @param: { $identifier } { value to search by }
  * @return: { array }
  */
  public function getDescription($identifier) {
    return $this->getField($identifier, 'description');
  }

  /**
  * Get status of category
  * @param: { $identifier } { value to search by }
  * @return: { array }
  */
  public function getStatus($identifier) {
    return $this->getField($identifier, 'status');
  }

  /**
  * List categories matching several dynamic parameters
  * @param: { $parameters } { array } { array of parameters }
  * This array can include any column from $this->table table which
  * is categories by default. You can specify fields and values in
  * $field => $value format which is then turned in MySQL conditions
  * Please refer to our Github page for usage examples
  * @return: { array }
  */
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

    if (isset($parameters['keyword'])) {
      $keyword = str_replace(array('?'), '', $parameters['keyword']);
      $keyword = mysqli_real_escape_string($this->database->mysqli(), $keyword);
      $this->database->where("MATCH (name, description) AGAINST ('$keyword' in boolean mode)");
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

  /**
  * List categories by specific status
  * @param: { $status } { string } { status to search }
  * @param: { $limit } { integer } { number or mysql style limit }
  * @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
  * @return: { array }
  */
  public function listByStatus($status, $limit, $parameters = false) {
    $parameters['status'] = $status;
    $parameters['limit'] = $limit;
    return $this->list($parameters);
  }

  /**
  * List categories by active status
  * @param: { $limit } { integer } { number or mysql style limit }
  * @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
  * @return: { array }
  */
  public function listActive($limit, $parameters = false) {
    return $this->listByStatus('active', $limit, $parameters);
  }

  /**
  * List categories by inactive status
  * @param: { $limit } { integer } { number or mysql style limit }
  * @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
  * @return: { array }
  */
  public function listInactive($limit, $parameters = false) {
    return $this->listByStatus('inactive', $limit, $parameters);
  }

  /**
  * Count total categories matching parameters
  * @param: { $parameters } { array } { false by default, any paramters to apply }
  * @return: { integer } { number of categories found }
  */
  public function count($parameters = false) {
    if ($parameters) {
      $parameters['count'] = true;
      return $this->list($parameters);
    } else {
      return $this->database->getValue($this->table, 'count(*)');
    }
  }

  /**
  * Count total active categories
  * @param: { $parameters } { array } { false by default, any paramters to apply }
  * @return: { integer } { number of categories found }
  */
  public function countActive($parameters = false) {
    $parameters['status'] = 'active';
    return $this->count($parameters);
  }

  /**
  * Count total inactive categories
  * @param: { $parameters } { array } { false by default, any paramters to apply }
  * @return: { integer } { number of categories found }
  */
  public function countInactive($parameters = false) {
    $parameters['status'] = 'inactive';
    return $this->count($parameters);
  }

  /**
  * Validate fields before updating category
  * @param: { $fields } { array } { fields and values to be updated }
  * @return: { boolean }
  */
  private function validateUpdate($fields) {
    $required = array('name', 'description');

    foreach ($fields as $key => $value) {
      if (!in_array($key, $this->KEYS)) {
        return $this->limbs->errors->add("Invalid field specified ($key)");
      }

      if (empty($value) && in_array($key, $required)) {
        return $this->limbs->errors->add("Field ($key) is required"); 
      }
    }

    return true;
  }

  /**
  * Validate form before inserting category
  * @param: { $fields } { array } { fields and values to insert }
  * @return: { boolean }
  */
  public function validateInsert($fields) {
    $required = array('name');

    if (!$this->users->authenticated()) {
      return $this->limbs->errors->add('You must be logged in before inserting');
    }

    foreach ($fields as $key => $value) {
      if (!in_array($key, $this->KEYS)) {
        return $this->limbs->errors->add("Invalid field specified ($key)");
      }

      if (empty($value) && in_array($key, $required)) {
        return $this->limbs->errors->add("Field ($key) is required"); 
      }
    }

    return true;
  }
  
  /**
  * Set category status to activve
  * @param: { $category } { string / integer } { category id or name }
  * @return: { boolean }
  */
  public function activate($category) {
    return $this->setField('status', 'active', $category, $this->column($category));
  }

  /**
  * Set category status to active for multiple categories
  * @param: { $categoriesArray } { mixed array } { list of category ids or names }
  * @param: { $identifer } { string } { specify if list contains ids or names }
  * @return: { boolean }
  */
  public function bulkActivate($categoriesArray, $identifier = 'name') {
    return $this->setFieldBulk('status', 'active', $categoriesArray, $identifier);
  }

  /**
  * Set category status to inactive
  * @param: { $category } { string / integer } { category id or name }
  * @return: { boolean }
  */
  public function deactivate($category) {
    return $this->setField('status', 'inactive', $category, $this->column($category));
  }

  /**
  * Set category status to inactive for multiple categories
  * @param: { $categoriesArray } { mixed array } { list of category ids or names }
  * @param: { $identifer } { string } { specify if list contains ids or names }
  * @return: { boolean }
  */
  public function bulkDeactivate($categoriesArray, $identifier = 'name') {
    return $this->setFieldBulk('status', 'inactive', $categoriesArray, $identifier);
  }

  /**
  * Delete a category and all media files belonging to it
  * @param: { $category } { string / integer } { category id or name }
  * @return: { boolean }
  */
  public function delete($category) {
    $column = $this->column($category);
    if ($column == 'name') {
      $category = $this->getId($category);
    }

    // set all videos to default category that were under this cat
    $this->database->where('category', $category);
    $updated = $this->database->update('videos', array('category' => '1'));
    if (!$updated) {
      return $this->limbs->errors->add("Unabel to update videos with category $category");
    }

    $this->database->where($column, $category);
    return $this->database->delete($this->table);
  }

  /**
  * Delete multiple categories
  * @param: { $categoriesArray } { mixed array } { list of category ids or names }
  * @return: { boolean }
  */
  public function bulkDelete($categoriesArray) {
    foreach ($categoriesArray as $key => $category) {
      if (!$this->delete($category)) {
        return $this->limbs->errors->add('Unable to delete ' . $category);
      }
    }

    return true;
  }

  /**
  * Insert a category into database
  * @param: { $form } { array } { raw form fields }
  * @return: { integer } { category id if inserted }
  */
  public function insert($form) {
    if ($this->validateInsert($form)) {
      return $this->database->insert($this->table, $form);
    }
  }

  /**
  * Update a category's fields
  * @param: { $identifier } { string / integer } { category id or name }
  * @param: { $details } { array } { an assoc array of fields and values to update }
  * @return: { boolean }
  */
  public function update($identifier, $details) {
    if ($this->validateUpdate($details)) {
      return $this->setFields($details, $identifier, $this->column($identifier));
    }
  }

  public function create($parameters) {
    return $this->database->insert($this->table, $parameters);
  }
}