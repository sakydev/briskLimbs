<?php

class Videos {
	
	function __construct() {
		
	}

	public function initialize($database = false) {
		global $limbs, $users;
		$this->limbs = $limbs;
		$this->users = $users;
		$this->database = $database ? $database : $this->limbs->database;
		$this->table = 'videos';
		$this->KEYS = $this->database->getColumnsList($this->table);
		$this->defaultLimit = 10;
	}

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

	public function owns($username, $video) {
		$creds = array($this->detectIdentifier($video) => $video, 'uploader_name' => $username);
	  if ($details = $this->exists($creds, false, array('id', 'uploader_name'))) {
	  	return $details;
	  }
	}

	public function keyExists($key) {
		return $this->exists('vkey', $key);
	}

	public function filenameExists($filename) {
		return $this->exists('filename', $filename);
	}

	private function detectIdentifier($identifier) {
		return is_numeric($identifier) ? 'id' : 'vkey';
	}

	public function getField($identifier, $field) {
		$this->database->where($this->detectIdentifier($identifier), $identifier);
		$results = $this->database->get($this->table, null, array($field));
		return isset($results['0'][$field]) ? $results['0'][$field] : false;
	}

	public function getFields($identifier, $fields) {
		$this->database->where($this->detectIdentifier($identifier), $identifier);
		$results = $this->database->get($this->table, null, is_array($fields) ? $fields : array($fields));
		return isset($results['0']) ? $results['0'] : false;
	}

	public function getDuration($identifier) {
		return $this->getField($identifier, 'duration');
	}

	public function getViews($identifier) {
		return $this->getField($identifier, 'views');
	}

	public function getTitle($identifier) {
		return $this->getField($identifier, 'title');
	}

	public function getStatus($identifier) {
		return $this->getField($identifier, 'status');
	}

	public function getUploader($identifier) {
		return $this->getField($identifier, 'uploader_name');
	}

	public function getThumbnails($identifier) {
		
	}

	/* get video by id or key */
	public function get($identifier, $type = false) {
		$this->database->where($type ? $type : $this->detectIdentifier($identifier), $identifier);
		return $this->database->getOne($this->table);
	}

	public function getById($id) {
		return $this->get($id, 'id');
	}

	public function getByKey($vKey) {
		return $this->get($vKey, 'vkey');
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

		if (isset($parameters['keyword'])) {
			$keyword = str_replace(array('?'), '', $parameters['keyword']);
      $keyword = mysqli_real_escape_string($this->database->mysqli(), $keyword);
			$this->database->where("MATCH (title, description) AGAINST ('$keyword' in boolean mode)");
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

	public function getTrending($limit, $parameters = array()) {
		$parameters['sort'] = 'views';
		$parameters['state'] = 'active';
		$parameters['status'] = 'successful';
		$parameters['limit'] = $limit;
		return $this->list($parameters);
	}

	public function getFresh($limit, $parameters = array()) {
		$parameters['sort'] = 'date';
		$parameters['state'] = 'active';
		$parameters['status'] = 'successful';
		$parameters['limit'] = $limit;
		return $this->list($parameters);
	}

	/* Upload section starts */
	private function validateUpload($fileData) {
		if (!$this->users->authenticated()) {
			return $this->limbs->errors->add('You must be logged in before uploading');
		}

		if (!strstr($fileData['type'], 'video')) {
			return $this->limbs->errors->add('Submitted file is not a video or invalid extension');
		}

		return true;
	}

	private function validateUpdate($fields) {
		$required = array('title', 'description');

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

	public function validateInsert($fields) {
		$required = array('title', 'description', 'filename', 'uploader_id', 'uploader_name');

		if (!$this->users->authenticated()) {
			return $this->limbs->errors->add('You must be logged in before uploading');
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

	// set('status', 'successful', 'sad2314', 'vkey');
	public function set($field, $value, $identifierValue, $identifier = 'vkey') {
		$this->database->where($identifier, $identifierValue);
		return $this->database->update($this->table, array($field => $value));
	}

	// update a single column of multiple videos
	public function bulkSet($field, $value, $identifierValueArray, $identifier = 'vkey') {
		$this->database->where($identifier, $identifierValueArray, 'IN');
		return $this->database->update($this->table, array($field => $value));
	}

	// update many columns of same row
	public function multipleSet($fields, $field, $value) {
    $this->database->where($field, $value);
    return $this->database->update($this->table, $fields);
  }
	
	public function validatePermissions($video) {
		if ($this->users->isAdmin() || $this->owns($video)) {
			return true;
		}
	}

	public function activate($video) {
		if (!$this->validatePermissions($video)) {
			return $this->limbs->errors->add("You don't have permissions to activate $video");
		}

		return $this->set('state', 'active', $video, $this->detectIdentifier($video));
	}

	public function bulkActivate($videosArray, $identifier = 'vkey') {
		return $this->bulkSet('state', 'active', $videosArray, $identifier);
	}

	public function deactivate($video) {
		if (!$this->validatePermissions($video)) {
			return $this->limbs->errors->add("You don't have permissions to deactivate $video");
		}

		return $this->set('state', 'inactive', $video, $this->detectIdentifier($video));
	}

	public function bulkDeactivate($videosArray, $identifier = 'vkey') {
		return $this->bulkSet('state', 'inactive', $videosArray, $identifier);
	}


	public function bulkDelete($videosArray) {
		foreach ($videosArray as $key => $video) {
			if (!$this->delete($video)) {
				return $this->limbs->errors->add('Unable to delete ' . $video);
			}
		}

		return true;
	}

	public function delete($video) {
		if (!$this->validatePermissions($video)) {
			return $this->limbs->errors->add("You don't have permissions to delete $video");
		}

		$results = $this->getFields($video, array('filename','date'));
		$filename = $results['filename'];
		$directory = directory($results['date']);
		$thumbnails = new Thumbnails($filename, $directory);
		$files = new Files($filename, $directory);
		if (!$thumbnails->delete()) {
			return $this->limbs->errors->add('Unable to delete thumbnails');
		}

		if (!$files->delete()) {
			return $this->limbs->errors->add('Unable to delete files');
		}

		$this->database->where($this->detectIdentifier($video), $video);
		return $this->database->delete($this->table);
	}

	public function createKey() {
		while (true) {
			$key = randomString(5) . '_' . randomString(4);
			if (!$this->keyExists($key)) {
				return $key;
			}
		}
	}

	public function createFilename() {
		while (true) {
			$filename = randomString(9) . 'x' . randomString(5);
			if (!$this->filenameExists($filename)) {
				return $filename;
			}
		}
	}

	public function upload($fileData) {
		if ($this->validateUpload($fileData)) {
			$directories = createDirectories();
			$filename = $this->createFilename();
      $temporaryPath = $directories['temporary'] . '/' . $filename . '.' . getExtension($fileData['name']);
      if (move_uploaded_file($fileData['tmp_name'], $temporaryPath) && file_exists($temporaryPath)) {
				$directory = $directories['combination'];
				$extension = getExtension($temporaryPath);

				$command = '/usr/bin/php ' . DAEMONS_DIRECTORY . "/conversion.php  filename=$filename directory=$directory extension=$extension > /dev/null 2>&1 &";
				# exit($commmand);
	      shell_exec($command);

        return array('filename' => $filename, 'path' => $temporaryPath, 'directory' => $directories['videos'], 'command' => $command);
      }
		}
	}

	public function insert($form) {
		if ($this->validateInsert($form)) {
			$prepare = array();
			$prepare['title'] = $form['title'];
			$prepare['filename'] = $form['filename'];
	    $prepare['date'] = date("Y/m/d H:i:s");
	    $prepare['vkey'] = $this->createKey();
	    $prepare['state'] = 'active';
	    $prepare['uploader_name'] = $this->users->username();
	    $prepare['uploader_id'] = $this->users->userId();
			return $this->database->insert($this->table, $prepare);
		}
	}

	public function update($identifier, $details) {
		if (!$this->validatePermissions($identifier)) {
			return $this->limbs->errors->add("You don't have permissions to update");
		}

		if ($this->validateUpdate($details)) {
			return $this->multipleSet($details, $this->detectIdentifier($identifier), $identifier);
		}
	}

	/* Upload section ends */

	public function setViews($video, $views = '+1') {
		$identifier = $this->detectIdentifier($video);
		if ($views == '+1') {
			return $this->set('views', $this->database->inc(1), $video, $identifier);
		} else {
			return $this->set('views', $views, $video, $identifier);
		}
	}

	public function setComments($video, $comments = '+1') {
		$identifier = $this->detectIdentifier($video);
		if ($comments == '+1') {
			return $this->set('comments', $this->database->inc(1), $video, $identifier);
		} else {
			return $this->set('comments', $comments, $video, $identifier);
		}
	}
}