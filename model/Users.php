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
	* Holds active table for users class
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
		$this->initialize();
	}

	public function initialize() {
		global $limbs;

		if (isset($_SESSION['username'])) {
      $this->username = $_SESSION['username'];
      $this->userId = $_SESSION['userId'];
      $this->level = $_SESSION['level'];
    }
    
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

	/**
	* Detect if a given value is user id or username
	* @param: { $identifier } { user id or username }
	* @return: { string / integer } { id or username }
	*/
	private function column($identifier) {
		if (is_numeric($identifier)) {
			return 'id';
		} elseif (strstr($identifier, '@')) {
			return 'email';
		} else {
			return 'username';
		}
	}

	/**
	* Get logged in user's ID
	* @return { integer }
	*/
	public function userId() {
		return $this->userId;
	}

	/**
	* Get logged in user's username
	* @return { string }
	*/
	public function username() {
		return $this->username;
	}

	/**
	* Get logged in user's level
	* @return { integer }
	*/
	public function level() {
		return $this->level;
	}

	/**
	* Check if a given user owns a given email
	* @param: { $username } { string } { username to check }
	* @param: { $email } { string / integer } { email to run check against }
	* @return: { boolean }
	*/
	public function ownsEmail($username, $email) {
		$creds = array('username' => $username, 'email' => $email);
	  if ($details = $this->exists($creds, false, array('id', 'username'))) {
	  	return $details;
	  }
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

		return $fetch ? $this->database->get($this->table, 1, $fetch) : $this->database->getValue($this->table, 'count(*)');
	}

	/**
	* Check if given id exists
	* @param: { $id } { integer } { id to check }
	* @return: { boolean }
	*/
	public function useridExists($id) {
		return $this->exists('id', $id);
	}

	/**
	* Check if given username exists
	* @param: { $username } { integer } { username to check }
	* @return: { boolean }
	*/
	public function usernameExists($username) {
		return $this->exists('username', $username);
	}

	/**
	* Check if given email exists
	* @param: { $email } { integer } { email to check }
	* @return: { boolean }
	*/
	public function emailExists($email) {
		return $this->exists('email', $email);
	}

	/**
	* Check if given activate_code exists
	* @param: { $code } { integer } { activate_code to check }
	* @return: { boolean }
	*/
	public function activationCodeExists($code) {
		$results = $this->exists('activate_code', $code, array('id'));
		if (!empty($results)) {
			return $results['0']['id']; 
		}
	}

	/**
	* Check if given reset_code exists
	* @param: { $code } { integer } { reset_code to check }
	* @return: { boolean }
	*/
	public function resetCodeExists($code) {
		$results = $this->exists('reset_code', $code, array('id'));
		if (!empty($results)) {
			return $results['0']['id']; 
		}
	}

	/**
	* Check if given string matches against id, email or username of existing user
	* @param: { $user } { mixed } { value to check }
	* @return: { boolean }
	*/
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
	* update multiple columns of multiple users
	* @param: { $fieldValueArray } { array } { field => value array to update }
	* @param: { $identifierValueArray } { array } { values array to search users by }
	* @param: { $identifier } { string } { username by default, column to search against }
	* @return: { boolean }
	*/
	public function setFieldsBulk($fieldValueArray, $identifierValueArray, $identifier = 'username') {
		$this->database->where($identifier, $identifierValueArray, 'IN');
		return $this->database->update($this->table, $fieldValueArray);
	}

	/**
	* Get all fields of user by id
	* @param: { $id } { integer } { id of user }
	* @return: { array }
	*/
	public function getById($id, $fields) {
		return $this->get($id, 'id');
	}

	/**
	* Get all fields of user by username
	* @param: { $username } { string } { username of user }
	* @return: { array }
	*/
	public function getByUsername($username) {
		return $this->get($username, 'username');
	}

	/**
	* Get all fields of user by email
	* @param: { $email } { string } { email of user }
	* @return: { array }
	*/
	public function getByEmail($email) {
		return $this->get($email, 'email');	
	}

	/**
	* Get ID of user
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getId($identifier) {
		return $this->getField($identifier, 'id');
	}

	/**
	* Get username of user
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getUsername($identifier) {
		return $this->getField($identifier, 'username');
	}

	/**
	* Get email of user
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getEmail($identifier) {
		return $this->getField($identifier, 'email');
	}

	/**
	* Get level of user
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getLevel($identifier) {
		return $this->getField($identifier, 'level');
	}

	/**
	* Get signup date of user
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getDate($identifier) {
		return $this->getField($identifier, 'date');
	}

	/**
	* Get status of user
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getStatus($identifier) {
		return $this->getField($identifier, 'status');
	}

	/**
	* Get date of birth of user
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getDob($identifier) {
		return $this->getField($identifier, 'dob');
	}

	/**
	* Get bio of user
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getBio($identifier) {
		return $this->getField($identifier, 'bio');
	}

	/**
	* Get Avatar of user
	* @param: { $username } { username to search by }
	* @return: { string }
	*/
	public function getAvatar($username) {
		$path = AVATARS_DIRECTORY . '/' . $username . '.jpg';
		if (file_exists($path)) {
			return CORE_URL . '/media/avatars/' . $username . '.jpg';
		} else {
			return CORE_URL . '/media/avatars/default.jpg';
		}
	}

	/**
	* Get Cover of user
	* @param: { $username } { username to search by }
	* @return: { string }
	*/
	public function getCover($username) {
		$path = COVERS_DIRECTORY . '/' . $username . '.jpg';
		if (file_exists($path)) {
			return CORE_URL . '/media/covers/' . $username . '.jpg';
		} else {
			return CORE_URL . '/media/covers/default.jpg';
		}
	}

	/**
	* List users matching several dynamic parameters
	* @param: { $parameters } { array } { array of parameters }
	* This array can include any column from $this->table table which
	* is users by default. You can specify fields and values in
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

	/**
	* List most recent users
	* @param: { $limit } { integer } { number or mysql style limit }
	* @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
	* @return: { array }
	*/
	public function listFresh($limit, $parameters = false) {
		$parameters['sort'] = 'date';
		$parameters['limit'] = $limit;
		return $this->list($parameters);
	}

	/**
	* List most users by specific state
	* @param: { $state } { string } { state to search }
	* @param: { $limit } { integer } { number or mysql style limit }
	* @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
	* @return: { array }
	*/
	public function listByState($state, $limit, $parameters = false) {
		$parameters['state'] = $state;
		$parameters['limit'] = $limit;
		return $this->list($parameters);
	}

	/**
	* List users by active state
	* @param: { $limit } { integer } { number or mysql style limit }
	* @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
	* @return: { array }
	*/
	public function listActive($limit, $parameters = false) {
		return $this->listByState('ok', $limit, $parameters);
	}

	/**
	* List users by inactive state
	* @param: { $limit } { integer } { number or mysql style limit }
	* @param: { $parameters } { array } { false by default, any additional paramters e.g select within range }
	* @return: { array }
	*/
	public function listInactive($limit, $parameters = false) {
		return $this->listByState('inactive', $limit, $parameters);
	}

	/**
	* Count total users matching parameters
	* @param: { $parameters } { array } { false by default, any paramters to apply }
	* @return: { integer } { number of users found }
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
	* Count total active users
	* @param: { $parameters } { array } { false by default, any paramters to apply }
	* @return: { integer } { number of users found }
	*/
	public function countActive($parameters = false) {
		$parameters['status'] = 'ok';
		return $this->list($parameters);
	}

	/**
	* Count total inactive users
	* @param: { $parameters } { array } { false by default, any paramters to apply }
	* @return: { integer } { number of users found }
	*/
	public function countInactive($parameters = false) {
		$parameters['status'] = 'inactive';
		return $this->list($parameters);
	}

	/**
	* Validate fields before creating user
	* @param: { $fields } { array } { fields and values to be inserted }
	* @return: { boolean }
	*/
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
	
	/**
	* Validate fields before updating user
	* @param: { $fields } { array } { fields and values to be updated }
	* @return: { boolean }
	*/
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

	/**
	* Set user state to activve
	* @param: { $user } { string / integer } { user id or username }
	* @return: { boolean }
	*/
	public function activate($user) {
		return $this->setField('status', 'ok', $user, is_numeric($user) ? 'id' : 'username');
	}

	/**
	* Set user state to active for multiple users
	* @param: { $usersArray } { mixed array } { list of user ids or usernames }
	* @param: { $identifer } { string } { specify if list contains ids or usernames }
	* @return: { boolean }
	*/
	public function bulkActivate($usersArray, $identifier = 'username') {
		return $this->setFieldBulk('status', 'ok', $usersArray, $identifier);
	}

	/**
	* Set user state to inactive
	* @param: { $user } { string / integer } { user id or username }
	* @return: { boolean }
	*/
	public function deactivate($user) {
		return $this->setField('status', 'inactive', $user, is_numeric($user) ? 'id' : 'username');
	}

	/**
	* Set user state to inactive for multiple users
	* @param: { $usersArray } { mixed array } { list of user ids or usernames }
	* @param: { $identifer } { string } { specify if list contains ids or usernames }
	* @return: { boolean }
	*/
	public function bulkDeactivate($usersArray, $identifier = 'username') {
		return $this->setFieldBulk('status', 'inactive', $usersArray, $identifier);
	}

	/**
	* Delete a user
	* @param: { $user } { string / integer } { user id or username }
	* @return: { boolean }
	*/
	public function delete($user) {
		if (!$this->isAdmin()) {
			return $this->limbs->errors->add("You don't have sufficient permissions for this");
		}

		if ($user == $this->username() || $user == $this->userId()) {
			return $this->limbs->errors->add("You can't delete your own account");
		}
		$this->database->where(is_numeric($user) ? 'id' : 'username', $user);
		return $this->database->delete($this->table);
	}

	/**
	* Delete multiple users
	* @param: { $usersArray } { mixed array } { list of user ids or usernames }
	* @return: { boolean }
	*/
	public function bulkDelete($usersArray) {
		foreach ($usersArray as $key => $user) {
			if ($user == 'undefined' || $user == $this->username() || $user == $this->userId()) {
				continue;
			}
			if (!$this->delete($user)) {
				return $this->limbs->errors->add('Unable to delete ' . $user);
			}
		}

		return true;
	}

	/**
	* Insert a user into database
	* @param: { $fields } { array } { raw form fields }
	* @return: { integer } { user id if inserted }
	*/
	public function create($fields) {
		if ($this->validate($fields)) {
			$fields['password'] = $this->securePassword($fields['password']);
			$fields['activate_code'] = $this->createActivateCode();
			$status = $this->database->insert($this->table, $fields);
			if ($status) {
				$message = getMessage('signup');
				$subject = prepareMessage($message['subject']);
				$messageBody = prepareMessage($message['message'], $fields);
				$mail = new Mail();
				$mail->send($subject, $messageBody, $fields['email'], $fields['username']);

				return $status;
			}
		}
	}

	/**
	* Update a user's fields
	* @param: { $identifier } { string / integer } { user id or username }
	* @param: { $details } { array } { an assoc array of fields and values to update }
	* @return: { boolean }
	*/
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

	/**
	* Authenticate user credentials
	* @param: { $username } { string } { username to check }
	* @param: { $password } { string } { password to check }
	* @return: { array } { id, status and level of existing user }
	*/
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

	/**
	* Create hashed password for database
	* @param: { string } { $password } { string password to hash }
	* @return: { string }
	*/
	public function securePassword($password) {
    return md5(md5(md5($password)));
  }

  /**
	* Login a user
	* @param: { $username } { string } { username to login }
	* @param: { $password } { string } { password of user }
	* @return: { integer }
  */
	public function login($username, $password) {
		if ($details = $this->authenticate($username, $password)) {
			$_SESSION['username'] = $username;
      $_SESSION['userId'] = $userId = $details['0']['id'];
      $_SESSION['level'] = $details['0']['level'];

      return $userId;
		}
	}

	/**
	* Check if a user is logged in
	* @return: { boolean }
	*/
	public function authenticated() {
		return !empty($this->userId) ? $this->userId : false;
	}

	/**
	* Check if a user is admin
	* @param: { $username } { string } { logged in user by default, username to check }
	* @return: { boolean }
	*/
	public function isAdmin($username = false) {
		return $this->authenticated() && $this->level() == 1 ? true : false;
	}

	/**
	* Upload user avatar
	* @param: { $formData } { array } { raw $_FILES object }
	* @return: { boolean }
	*/
	public function uploadAvatar($formData) {
		$file = AVATARS_DIRECTORY . '/' . $this->username() . '.jpg';
		if (file_exists($file)) { @unlink($file); }
		return move_uploaded_file($formData['tmp_name'], $file) ? $file : $this->limbs->errors->add('Failed to upload avatar');
	}

	/**
	* Upload user channel cover
	* @param: { $formData } { array } { raw $_FILES object }
	* @return: { boolean }
	*/
	public function uploadCover($formData) {
		$file = COVERS_DIRECTORY . '/' . $this->username() . '.jpg';
		if (file_exists($file)) { @unlink($file); }
		return move_uploaded_file($formData['tmp_name'], $file) ? $file : $this->limbs->errors->add('Failed to upload cover');
	}

	/**
	* Create hashed reset code for database
	* @return: { string }
	*/
	public function createResetCode() {
    return md5(md5(randomString(15)));
  }

  /**
	* Create hashed activation code for database
	* @return: { string }
	*/
	public function createActivateCode() {
    return md5(md5(randomString(13)));
  }

  public function requestResetPassword($user) {
  	$code = $this->createResetCode();
  	$column = $this->column($user);
  	$status = $this->setField('reset_code', $code, $user, $column);
  	$email = $column != 'email' ? $this->getEmail($user) : $user;
  	$username = $column != 'username' ? $this->getUsername($user) : $user;

  	if ($status) {
  		$fields = array('username' => $username, 'email' => $email, 'reset_code' => $code);
  		$message = getMessage('reset');
			$subject = prepareMessage($message['subject'], $fields);
			$messageBody = prepareMessage($message['message'], $fields);
			$mail = new Mail();
			if ($mail->send($subject, $messageBody, $email, $username)) {
				return $status;
			}

			return $this->limbs->errors->add('Unable to reset password');
  	}
  }

  public function resetPassword($user, $password) {
  	$password = $this->securePassword($password);
  	return $this->setField('password', $password, $user, is_numeric($user) ? 'id' : 'username');
  }
}