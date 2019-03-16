<?php

/**
* Name: Videos
* Description: Core class for handling videos CRUD and other actions
* @author: Saqib Razzaq
* @since: v1, Feburary, 2019
* @link: https://github.com/briskLimbs/briskLimbs/blob/master/model/Videos.php
*/

class Videos {

	/**
	* Initializes basic variables and prepares class to be used
	* @param: { $database } { false, database object if avaible }
	*/
	public function initialize($database = false) {
		global $limbs, $users;
		$this->limbs = $limbs;
		$this->users = $users;
		$this->database = $database ? $database : $this->limbs->database;
		$this->table = 'videos';
		$this->KEYS = $this->database->getColumnsList($this->table);
		$this->defaultLimit = 10;
	}

	/**
	* Detect if a given value is video id or public vkey
	* @param: { $identifier } { video id or public key }
	* @return: { string / integer } { id or vkey }
	*/
	private function column($identifier) {
		return is_numeric($identifier) ? 'id' : 'vkey';
	}

	/**
	* Check if a given user owns a given video
	* @param: { $username } { string } { username to check }
	* @param: { $video } { string / integer } { video id or vkey to run check against }
	* @return: { boolean }
	*/
	public function owns($username, $video) {
		$creds = array($this->column($video) => $video, 'uploader_name' => $username);
	  return $this->exists($creds, false, array('id', 'uploader_name'));
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
	* Check if given vkey exists
	* @param: { $key } { string } { vkey to check }
	* @return: { boolean }
	*/
	public function keyExists($key) {
		return $this->exists('vkey', $key);
	}

	/**
	* Check if given filename exists
	* @param: { $filename } { string } { filename to check }
	* @return: { boolean }
	*/
	public function filenameExists($filename) {
		return $this->exists('filename', $filename);
	}

	/** 
	* Get a single video
	* @param: { $identifier } { string / integer } { value to search by }
	* @param: { $type } { string } { column to search against, auto detect when false }
	* @return: { array } { results array when matched }
	*/
	public function get($identifier, $type = false) {
		$this->database->where($type ? $type : $this->column($identifier), $identifier);
		return $this->database->getOne($this->table); // getOne means limit 1
	}

	/**
	* Get a single field for a single video
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
	* Get a multiple fields for a single video
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
	* Update a single field of single video
	* @param: { $field } { string } { field to update }
	* @param: { $value } { mixed } { new value to set }
	* @param: { $identiferValue } { mixed } { value to search video by }
	* @param: { $identifier } { string } { vkey by default, column to search against }
	* @return: { boolean }
	*/
	public function setField($field, $value, $identifierValue, $identifier = 'vkey') {
		$this->database->where($identifier, $identifierValue);
		return $this->database->update($this->table, array($field => $value));
	}

	/**
	* update a single field of multiple videos
	* @param: { $field } { string } { field to update }
	* @param: { $value } { mixed } { new value to set }
	* @param: { $identifierValueArray } { array } { values array to search videos by }
	* @param: { $identifier } { string } { vkey by default, column to search against }
	* @return: { boolean }
	*/
	public function setFieldBulk($field, $value, $identifierValueArray, $identifier = 'vkey') {
		$this->database->where($identifier, $identifierValueArray, 'IN');
		return $this->database->update($this->table, array($field => $value));
	}

	/**
	* update multiple fields of single video
	* @param: { $fieldValueArray } { array } { field => value array to update }
	* @param: { $identiferValue } { mixed } { value to search video by }
	* @param: { $identifier } { string } { vkey by default, column to search against }
	* @return: { boolean }
	*/
	public function setFields($fieldValueArray, $identifierValue, $identifier = 'vkey') {
		$this->database->where($identifier, $identifierValue);
		return $this->database->update($this->table, $fieldValueArray);
	}

	/**
	* update multiple columns of multiple videos
	* @param: { $fieldValueArray } { array } { field => value array to update }
	* @param: { $identifierValueArray } { array } { values array to search videos by }
	* @param: { $identifier } { string } { vkey by default, column to search against }
	* @return: { boolean }
	*/
	public function setFieldsBulk($fieldValueArray, $identifierValueArray, $identifier = 'vkey') {
		$this->database->where($identifier, $identifierValueArray, 'IN');
		return $this->database->update($this->table, $fieldValueArray);
	}

	/**
	* Get all fields of video by id
	* @param: { $id } { integer } { id of video }
	* @return: { array }
	*/
	public function getById($id) {
		return $this->get($id, 'id');
	}

	/**
	* Get all fields of video by key
	* @param: { $key } { integer } { key of video }
	* @return: { array }
	*/
	public function getByKey($vKey) {
		return $this->get($vKey, 'vkey');
	}

	/**
	* Get all fields of video by filename
	* @param: { $filename } { integer } { filename of video }
	* @return: { array }
	*/
	public function getByFilename($vKey) {
		return $this->get($vKey, 'vkey');
	}

	/**
	* Get ID of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getId($identifier) {
		return $this->getField($identifier, 'id');
	}

	/**
	* Get key of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getKey($identifier) {
		return $this->getField($identifier, 'vkey');
	}

	/**
	* Get Filename of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getFilename($identifier) {
		return $this->getField($identifier, 'filename');
	}

	/**
	* Get video uploader's ID
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getUploaderId($identifier) {
		return $this->getField($identifier, 'uploader_id');
	}

	/**
	* Get video uploader's name
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getUploaderName($identifier) {
		return $this->getField($identifier, 'uploader_name');
	}

	/**
	* Get publish date of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getDate($identifier) {
		return $this->getField($identifier, 'date');
	}

	/**
	* Get title of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getTitle($identifier) {
		return $this->getField($identifier, 'title');
	}

	/**
	* Get description of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getDescription($identifier) {
		return $this->getField($identifier, 'description');
	}

	/**
	* Get scope of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getScope($identifier) {
		return $this->getField($identifier, 'scope');
	}

	/**
	* Get featured status of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getFeatured($identifier) {
		return $this->getField($identifier, 'featured');
	}

	/**
	* Get processing status of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getStatus($identifier) {
		return $this->getField($identifier, 'status');
	}

	/**
	* Get qualities of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getQualities($identifier) {
		return $this->getField($identifier, 'qualities');
	}

	/**
	* Get duration of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getDuration($identifier) {
		return $this->getField($identifier, 'duration');
	}

	/**
	* Get thumbnails count of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getThumbnailsCount($identifier) {
		return $this->getField($identifier, 'thumbnails_count');
	}

	/**
	* Get total views of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getViews($identifier) {
		return $this->getField($identifier, 'views');
	}

	/**
	* Get total comments of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getComments($identifier) {
		return $this->getField($identifier, 'comments');
	}

	/**
	* Get state of video
	* @param: { $identifier } { value to search by }
	* @return: { array }
	*/
	public function getState($identifier) {
		return $this->getField($identifier, 'state');
	}

	public function list($parameters = false) {
		if (is_array($parameters)) {
			if (!$this->users->isAdmin()) {
				$parameters['state'] = isset($parameters['state']) ? $parameters['state'] : 'active';
				$parameters['status'] = isset($parameters['status']) ? $parameters['status'] : 'successful';

				if (!isset($parameters['uploader_name']) && !isset($parameters['uploader_id'])) {
					$parameters['scope'] = 'public';
				}
			}

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

	public function listTrending($limit, $parameters = false) {
		$parameters['sort'] = 'views';
		$parameters['limit'] = $limit;
		return $this->list($parameters);
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

  public function listByStatus($status, $limit, $parameters = false) {
		$parameters['status'] = $status;
		$parameters['limit'] = $limit;
		return $this->list($parameters);
	}

	public function listSuccessful($limit, $parameters = false) {
		return $this->listByStatus('successful', $limit, $parameters);
	}

	public function listPending($limit, $parameters = false) {
		return $this->listByStatus('pending', $limit, $parameters);
	}

	public function listFailed($limit, $parameters = false) {
		return $this->listByStatus('failed', $limit, $parameters);
	}

	public function listByScope($scope, $limit, $parameters = false) {
		$parameters['scope'] = $scope;
		$parameters['limit'] = $limit;
		return $this->list($parameters);
	}

	public function listPublic($limit, $parameters = false) {
		return $this->listByScope('public', $limit, $parameters);
	}

	public function listPrivate($limit, $parameters = false) {
		return $this->listByScope('private', $limit, $parameters);
	}

	public function listPublic($limit, $parameters = false) {
		return $this->listByScope('unlist', $limit, $parameters);
	}

	public function listByUploader($uploader, $limit, $parameters = false) {
		$parameters[is_numeric($uploader) ? 'uploader_id' : 'uploader_name'] = $uploader;
		$parameters['limit'] = $limit;
		return $this->list($parameters);
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
		$parameters['state'] = 'active';
		return $this->count($parameters);
	}

	public function countInactive($parameters = false) {
		$parameters['state'] = 'inactive';
		return $this->count($parameters);
	}

	public function countSuccessful($parameters = false) {
		$parameters['status'] = 'successful';
		return $this->count($parameters);
	}

	public function countPending($parameters = false) {
		$parameters['status'] = 'pending';
		return $this->count($parameters);
	}
	
	public function countFailed($parameters = false) {
		$parameters['status'] = 'failed';
		return $this->count($parameters);
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
	
	public function validatePermissions($video) {
		if ($this->users->isAdmin() || $this->owns($this->users->username(), $video)) {
			return true;
		}
	}

	public function activate($video) {
		if (!$this->validatePermissions($video)) {
			return $this->limbs->errors->add("You don't have permissions to activate $video");
		}

		return $this->setField('state', 'active', $video, $this->column($video));
	}

	public function bulkActivate($videosArray, $identifier = 'vkey') {
		return $this->setFieldBulk('state', 'active', $videosArray, $identifier);
	}

	public function deactivate($video) {
		if (!$this->validatePermissions($video)) {
			return $this->limbs->errors->add("You don't have permissions to deactivate $video");
		}

		return $this->setField('state', 'inactive', $video, $this->column($video));
	}

	public function bulkDeactivate($videosArray, $identifier = 'vkey') {
		return $this->setFieldBulk('state', 'inactive', $videosArray, $identifier);
	}

	public function feature($video) {
		if (!$this->validatePermissions($video)) {
			return $this->limbs->errors->add("You don't have permissions to feature $video");
		}

		return $this->setField('featured', 'yes', $video, $this->column($video));
	}


	public function unfeature($video) {
		if (!$this->validatePermissions($video)) {
			return $this->limbs->errors->add("You don't have permissions to unfeature $video");
		}

		return $this->setField('featured', 'no', $video, $this->column($video));
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

		$this->database->where($this->column($video), $video);
		return $this->database->delete($this->table);
	}

	public function bulkDelete($videosArray) {
		foreach ($videosArray as $key => $video) {
			if (!$this->delete($video)) {
				return $this->limbs->errors->add('Unable to delete ' . $video);
			}
		}

		return true;
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

				$command = $this->limbs->settings->get('php') . ' ' . DAEMONS_DIRECTORY . "/conversion.php  filename=$filename directory=$directory extension=$extension > /dev/null 2>&1 &";
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
			return $this->setFields($details, $identifier, $this->column($identifier));
		}
	}

	/* Upload section ends */

	public function setViews($video, $views = '1') {
		return $this->setField('views', $this->database->inc($views), $video, $this->column($video));
	}

	public function setComments($video, $comments = '1') {
		return $this->setField('comments',  $this->database->inc($comments), $video, $this->column($video));
	}
}