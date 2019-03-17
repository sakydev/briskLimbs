<?php

/**
* Name: Users
* Description: Core class for handling users CRUD and other actions
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Users.php
*/

class Users {
	/*
	* Holds username for currently logged in user
	*/
	public $username;

	/*
	* Holds userid for currently logged in user
	*/
	public $userId;

	/*
	* Holds global Limbs object
	*/
	protected $limbs;

	/*
	* Holds global Database object
	*/
	protected $database;

	/*
	* Holds active table for Videos class
	*/
	protected $table;

	/*
	* Holds most basic keys for table
	*/
	protected $basicKeys;

	/*
	* Holds list of columns for active table
	*/
	private $KEYS;

	/*
	* Holds default listing limit for class
	*/
	private $defaultLimit;

	function __construct() {
		if (isset($_SESSION['username'])) {
      $this->username = $_SESSION['username'];
      $this->userId = $_SESSION['userId'];
      $this->level = $_SESSION['level'];
    }
	}

	public function initialize() {
		global $limbs;
		$this->limbs = $limbs;
		$this->database = $limbs->database;
		$this->table = 'users';
		$this->KEYS = $this->database->getColumnsList($this->table);
		$this->basicKeys = array('id', 'username', 'level');
		$this->defaultLimit = 10;

		if ($this->username()) {
			$details = $this->getByUsername($this->username());
			$details['avatar'] = $this->getAvatar($this->username());
			$details['cover'] = $this->getCover($this->username());
			$limbs->addTemplateParameter('_auth', $details);
		}
	}

	private function column($identifier) {
		return is_numeric($identifier) ? 'id' : 'username';
	}

	public function userId() {
		return $this->userId;
	}

	public function username() {
		return $this->username;
	}

	public function level() {
		return $this->level;
	}

	public function ownsEmail($username, $email) {
		$creds = array('username' => $username, 'email' => $email);
	  if ($details = $this->exists($creds, false, array('id', 'username'))) {
	  	return $details;
	  }
	}

	public function exists($fields, $value = false, $fetch = false) {
		if (is_array($fields)) {
			foreach ($fields as $key => $value) {
				$this->database->where($key, $value);
			}
		} else {
			$this->database->where($fields, $value);
		}

		return $fetch ? $this->database->get($this->table, 1, $fetch) : $this->database->getValue($this->table, 'count(*)');
	}

	public function useridExists($id) {
		return $this->exists('id', $id);
	}

	public function usernameExists($username) {
		return $this->exists('username', $username);
	}

	public function emailExists($email) {
		return $this->exists('email', $email);
	}


	public function userExists($user) {
		$column = $this->column($user);
		if ($column == 'id') {
			return $this->useridExists($user);
		}

		return $this->emailExists($user) || $this->usernameExists($user) ? true : false;
	}

	/** 
	* Get a single user
	* @param: { $identifier } { string / integer } { value to search by }
	* @param: { $type } { string } { column to search against, auto detect when false }
	* @return: { array } { results array when matched }
	*/
	public function get($identifier, $type = false) {
		$this->database->where($type ? $type : $this->column($identifier), $identifier);
		return $this->database->getOne($this->table); // getOne means limit 1
	}

	/**
	* Get a single field for a single user
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
	* Get a multiple fields for a single user
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
	* Update a single field of single user
	* @param: { $field } { string } { field to update }
	* @param: { $value } { mixed } { new value to set }
	* @param: { $identiferValue } { mixed } { value to search user by }
	* @param: { $identifier } { string } { username by default, column to search against }
	* @return: { boolean }
	*/
	public function setField($field, $value, $identifierValue, $identifier = 'username') {
		$this->database->where($identifier, $identifierValue);
		return $this->database->update($this->table, array($field => $value));
	}

	/**
	* update a single field of multiple users
	* @param: { $field } { string } { field to update }
	* @param: { $value } { mixed } { new value to set }
	* @param: { $identifierValueArray } { array } { values array to search users by }
	* @param: { $identifier } { string } { username by default, column to search against }
	* @return: { boolean }
	*/
	public function setFieldBulk($field, $value, $identifierValueArray, $identifier = 'username') {
		$this->database->where($identifier, $identifierValueArray, 'IN');
		return $this->database->update($this->table, array($field => $value));
	}

	/**
	* update multiple fields of single user
	* @param: { $fieldValueArray } { array } { field => value array to update }
	* @param: { $identiferValue } { mixed } { value to search user by }
	* @param: { $identifier } { string } { username by default, column to search against }
	* @return: { boolean }
	*/
	public function setFields($fieldValueArray, $identifierValue, $identifier = 'username') {
		$this->database->where($identifier, $identifierValue);
		return $this->database->update($this->table, $fieldValueArray);
	}

	/**
	* update multiple columns of multiple videos
	* @param: { $fieldValueArray } { array } { field => value array to update }
	* @param: { $identifierValueArray } { array } { values array to search videos by }
	* @param: { $identifier } { string } { username by default, column to search against }
	* @return: { boolean }
	*/
	public function setFieldsBulk($fieldValueArray, $identifierValueArray, $identifier = 'username') {
		$this->database->where($identifier, $identifierValueArray, 'IN');
		return $this->database->update($this->table, $fieldValueArray);
	}

	public function getById($id, $fields) {
		return $this->get($id, 'id');
	}

	public function getByUsername($username) {
		return $this->get($username, 'username');
	}

	public function getByEmail($email) {
		return $this->get($email, 'email');	
	}

	public function getId() {
		return $this->getField($identifier, 'id');
	}

	public function getUsername() {
		return $this->getField($identifier, 'username');
	}

	public function getEmail() {
		return $this->getField($identifier, 'email');
	}

	public function getLevel() {
		return $this->getField($identifier, 'level');
	}

	public function getDate() {
		return $this->getField($identifier, 'date');
	}

	public function getStatus() {
		return $this->getField($identifier, 'status');
	}

	public function getDob() {
		return $this->getField($identifier, 'dob');
	}

	public function getBio() {
		return $this->getField($identifier, 'bio');
	}

	public function getAvatar($username) {
		$path = AVATARS_DIRECTORY . '/' . $username . '.jpg';
		if (file_exists($path)) {
			return CORE_URL . '/media/avatars/' . $username . '.jpg';
		} else {
			return CORE_URL . '/media/avatars/default.jpg';
		}
	}

	public function getCover($username) {
		$path = COVERS_DIRECTORY . '/' . $username . '.jpg';
		if (file_exists($path)) {
			return CORE_URL . '/media/covers/' . $username . '.jpg';
		} else {
			return CORE_URL . '/media/covers/default.jpg';
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

	public function listFresh($limit, $parameters = false) {
		$parameters['sort'] = 'date';
		$parameters['limit'] = $limit;
		return $this->list($parameters);
	}

	public function listByState($state, $limit, $parameters = false) {
		$parameters['state'] = $state;
		$parameters['limit'] = $limit;
		return $this->list($parameters);
	}

	public function listActive($limit, $parameters = false) {
		return $this->listByState('active', $limit, $parameters);
	}

	public function listInactive($limit, $parameters = false) {
		return $this->listByState('inactive', $limit, $parameters);
	}

	public function count($parameters = false) {
		if ($parameters) {
			$parameters['count'] = true;
			return $this->list($parameters);
		} else {
			return $this->database->getValue($this->table, 'count(*)');
		}
	}

	public function countActive($parameters = false) {
		$parameters['status'] = 'ok';
		return $this->list($parameters);
	}

	public function countInactive($parameters = false) {
		$parameters['status'] = 'inactive';
		return $this->list($parameters);
	}

	public function validate($fields) {
		if (!$this->limbs->settings->allowSignups()) {
			return $this->limbs->errors->add('Singups are not allowed at the moment');
		}
		if (empty($fields['password'])) {
			return $this->limbs->errors->add('Invalid password specified');
		}

		if (empty($fields['email'])) {
			return $this->limbs->errors->add('Invalid email specified');	
		}

		if ($this->usernameExists($fields['username'])) {
			return $this->limbs->errors->add('User already exists');
		}

		if ($this->emailExists($fields['email'])) {
			return $this->limbs->errors->add('Email already exists');
		}

		foreach ($fields as $key => $value) {
			if (!in_array($key, $this->KEYS)) {
				return $this->limbs->errors->add("Invalid field specified ($key)");
			}
		}

		return true;
	}
	
	private function validateUpdate($fields) {
		$required = array('email', 'password', 'new_password', 'confirm_password');
		if (!$this->authenticated()) {
			return $this->limbs->errors->add('You must be logged in before updating');
		}

		foreach ($fields as $key => $value) {
			if (!in_array($key, $this->KEYS) && !in_array($key, $required)) {
				return $this->limbs->errors->add("Invalid field specified ($key)");
			}

			if (empty($value) && in_array($key, $required)) {
				return $this->limbs->errors->add("Field ($key) is required");	
			}
		}

		if (isset($fields['password'])) {
	    if ($fields['new_password'] != $fields['confirm_password']) {
	    	return $this->limbs->errors->add("New passwords don't match");
	    } else if (!$this->authenticate($this->username(), $fields['password'])) {
	    	return $this->limbs->errors->add("Current password is incorrect");
	    }
	  }

	  if (isset($fields['email'])) {
	  	if ($this->emailExists($fields['email'])) {
	  		if (!$this->ownsEmail($this->username(), $fields['email']) && !$this->isAdmin()) {
	  			return $this->limbs->errors->add('Email already exists');
	  		}
	  	}
	  }

		return true;
	}

	public function activate($video) {
		return $this->setField('status', 'ok', $video, is_numeric($video) ? 'id' : 'username');
	}

	public function bulkActivate($videosArray, $identifier = 'username') {
		return $this->setFieldBulk('status', 'ok', $videosArray, $identifier);
	}

	public function deactivate($video) {
		return $this->setField('status', 'inactive', $video, is_numeric($video) ? 'id' : 'username');
	}

	public function bulkDeactivate($videosArray, $identifier = 'username') {
		return $this->setFieldBulk('status', 'inactive', $videosArray, $identifier);
	}

	public function delete($video) {
		$this->database->where(is_numeric($video) ? 'id' : 'username', $video);
		return $this->database->delete($this->table);
	}

	public function create($fields) {
		if ($this->validate($fields)) {
			$fields['password'] = $this->securePassword($fields['password']);
			$fields['status'] = 'ok';
			return $this->database->insert($this->table, $fields);
		}
	}

	public function update($identifier, $details) {
		if ($this->validateUpdate($details)) {
			if (isset($details['password'])) {
				$details['password'] = $this->securePassword($details['new_password']);
	      unset($details['new_password']);
	      unset($details['confirm_password']);
			}

			return $this->setFields($details, $identifier, $this->column($identifier));
		}
	}

	public function authenticate($username, $password) {
		$credentials = array('username' => $username, 'password' => $this->securePassword($password));
		if ($results = $this->exists($credentials, false, array('id', 'status', 'level'))) {
			switch ($results['0']['status']) {
				case 'pending':
					return $this->limbs->errors->add('Your must activate your account first');
					break;
				case 'banned':
					return $this->limbs->errors->add('Your account has been banned');
					break;
				
				default:
					break;
			}
		} else {
			return $this->limbs->errors->add('Invalid username or password');
		}

		return $results;
	}

	public function securePassword($password) {
    return md5(md5(md5($password)));
  }

	public function login($username, $password) {
		if ($details = $this->authenticate($username, $password)) {
			$_SESSION['username'] = $username;
      $_SESSION['userId'] = $userId = $details['0']['id'];
      $_SESSION['level'] = $details['0']['level'];

      return $userId;
		}
	}

	public function authenticated() {
		return !empty($this->userId) ? $this->userId : false;
	}

	public function isAdmin($username = false) {
		return $this->authenticated() && $this->level() == 1 ? true : false;
	}

	public function uploadAvatar($formData) {
		$file = AVATARS_DIRECTORY . '/' . $this->username() . '.jpg';
		if (file_exists($file)) { @unlink($file); }
		return move_uploaded_file($formData['tmp_name'], $file) ? $file : $this->limbs->errors->add('Failed to upload avatar');
	}

	public function uploadCover($formData) {
		$file = COVERS_DIRECTORY . '/' . $this->username() . '.jpg';
		if (file_exists($file)) { @unlink($file); }
		return move_uploaded_file($formData['tmp_name'], $file) ? $file : $this->limbs->errors->add('Failed to upload cover');
	}
}