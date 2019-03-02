<?php

class Users {
	public $username;
	public $userId;

	function __construct() {
		if (isset($_SESSION['username'])) {
      $this->username = $_SESSION['username'];
      $this->userId = $_SESSION['userId'];
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
			$limbs->addTemplateParameter('_auth', $this->getByUsername($this->username(), $this->basicKeys));
		}
	}

	public function userId() {
		return $this->userId;
	}

	public function username() {
		return $this->username;
	}

	public function isAdmin($username = false) {
		if ($this->authenticated()) {
			$this->database->where('username', $username ? $username : $this->username());
			$this->database->where('level', '1');

			return $this->database->getValue($this->table, 'count(*)');
		}
	}

	public function authenticated() {
		return !empty($this->userId) ? $this->userId : false;
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

	public function usernameExists($username) {
		return $this->exists('username', $username);
	}

	public function emailExists($email) {
		return $this->exists('email', $email);
	}

	public function userExists($user) {
		$this->database->where('email', $user);
		$this->database->orWhere('username', $user);
		return $this->database->getValue($this->table, 'count(*)');
	}

	public function securePassword($password) {
    return md5(md5(md5($password)));
  }

	public function validate($fields) {
		if (!$this->settings->allowSignups()) {
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

	public function getBy($field, $value, $fields = false) {
		$this->database->where($field, $value);
		return $this->database->get($this->table, 1, $fields ? $fields : $this->KEYS);
	}

	public function get($user, $fields = false) {
		$this->database->where('username', $user);
		$this->database->orWhere('id', $user);
		return $this->database->get($this->table, false, $fields ? $fields : $this->KEYS);
	}

	public function getByUsername($username, $fields = false) {
		$results = $this->getBy('username', $username, $fields);
		return isset($results['0']) ? $results['0'] : false;
	}

	public function getById($id, $fields) {
		$results = $this->getBy('id', $id, $fields);
		return isset($results['0']) ? $results['0'] : false;
	}

	public function create($fields) {
		if ($this->validate($fields)) {
			$fields['password'] = $this->securePassword($fields['password']);
			return $this->database->insert($this->table, $fields);
		}
	}

	public function authenticate($username, $password) {
		$credentials = array('username' => $username, 'password' => $this->securePassword($password));
		if ($results = $this->exists($credentials, false, array('id', 'status'))) {
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

		return true;
	}

	public function login($username, $password) {
		if ($userId = $this->authenticate($username, $password)) {
			$_SESSION['username'] = $username;
      $_SESSION['userId'] = $userId;

      return $userId;
		}
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

	// set('status', 'successful', 'sad2314', 'vkey');
	public function set($field, $value, $identifierValue, $identifier = 'username') {
		$this->database->where($identifier, $identifierValue);
		return $this->database->update($this->table, array($field => $value));
	}

	// update a single column of multiple videos
	public function bulkSet($field, $value, $identifierValueArray, $identifier = 'username') {
		$this->database->where($identifier, $identifierValueArray, 'IN');
		return $this->database->update($this->table, array($field => $value));
	}
	
	public function activate($video) {
		return $this->set('status', 'ok', $video, is_numeric($video) ? 'id' : 'username');
	}

	public function bulkActivate($videosArray, $identifier = 'username') {
		return $this->bulkSet('status', 'ok', $videosArray, $identifier);
	}

	public function deactivate($video) {
		return $this->set('status', 'inactive', $video, is_numeric($video) ? 'id' : 'username');
	}

	public function bulkDeactivate($videosArray, $identifier = 'username') {
		return $this->bulkSet('status', 'inactive', $videosArray, $identifier);
	}

	public function delete($video) {
		$this->database->where(is_numeric($video) ? 'id' : 'username', $video);
		return $this->database->delete($this->table);
	}
}